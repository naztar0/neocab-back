<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use League\OAuth2\Client\Provider\GenericProvider as OAuth2Client;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use PHPOpenSourceSaver\JWTAuth\JWTAuth;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use App\Services\AuthService;
use App\Models\User;
use App\Http\Requests\LoginUserRequest;

class AuthController extends Controller
{
    public function __construct(
        private readonly OAuth2Client $oauthClient,
        private readonly Graph        $graphClient,
        private readonly AuthService  $authService,
        private readonly JWTAuth $jwt
    )
    {
    }

    public function getAzureAuthorizationUrl(): Response
    {
        $authUrl = $this->oauthClient->getAuthorizationUrl();

        return response(['url' => $authUrl]);
    }

    public function azureCallback(Request $request): RedirectResponse
    {
        $authCode = $request->query('code');
        if (!isset($authCode)) {
            return redirect(config('services.app.url'))
                ->with('error', $request->query('error'))
                ->with('errorDetail', $request->query('error_description'));
        }
        try {
            $accessToken = $this->oauthClient->getAccessToken('authorization_code', ['code' => $authCode]);

            $this->graphClient->setAccessToken($accessToken->getToken());

            $user = $this->graphClient->createRequest('GET', '/me')->setReturnType(Model\User::class)->execute();

            session()->regenerate();
            session()->put('azure_user_id', $user->getId());

            User::updateOrCreate(
                ['email' => strtolower($user->getMail())],
                ['azure_id' => $user->getId(), 'email' => strtolower($user->getMail())]
            );

            return redirect(config('services.app.url') . '/login?ms=1');
        } catch (IdentityProviderException $e) {
            return redirect(config('services.app.url'))
                ->with('error', 'Error requesting access token')
                ->with('errorDetail', json_encode($e->getResponseBody()));
        }
    }

    public function loginViaAzure(): Response
    {
        if (!$user_id = session()->get('azure_user_id')) {
            return response(['error' => 'Unauthorized'], 401);
        }
        session()->invalidate();

        if (!$user = User::where('azure_id', $user_id)->first()) {
            return response(['error' => 'Unauthorized'], 401);
        }
        if (!$user->student_id && in_array($user->role, [User::ROLE_STUDENT, User::ROLE_HEADMAN])) {
            return response(['error' => 'Native authorization required'], 403);
        }

        $token = $this->jwt->fromUser($user);

        return response([
            'status' => 'success',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
                'expires_in' => Auth::factory()->getTTL() * 60,
            ]
        ]);
    }

    public function loginViaCabinet(LoginUserRequest $request): Response
    {
        $credentials = $request->only('email', 'password');
        $credentials['email'] = strtolower($credentials['email']);
        $credentials['pass'] = $credentials['password'];

        $user = User::where(['email' => $credentials['email'], 'pass' => $credentials['pass']])->first();
        if (!$user) {
            if ($this->authService->checkAuthValidationRequest($credentials)) {
                return response(['message' => 'Pending validation'], 403);
            } else {
                return response(['message' => 'Unauthorized'], 401);
            }
        }

        $token = $this->jwt->fromUser($user);

        return response([
                'status' => 'success',
                'user' => $user,
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                    'expires_in' => Auth::factory()->getTTL() * 60,
                ]
            ]);
    }

    public function logout()
    {
        Auth::logout();
        return response([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response([
            'status' => 'success',
            'user' => Auth::user(),
            'authorization' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
                'expires_in' => Auth::factory()->getTTL() * 60,
            ]
        ]);
    }
}
