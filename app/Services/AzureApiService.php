<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Microsoft\Graph\Exception\GraphException;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model\User as GraphUser;
use Microsoft\Graph\Model\Event as GraphEvent;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class AzureApiService
{
    private Graph $app;

    public function __construct(
        private readonly Client $client,
        private readonly Graph $graphApp)
    {
        $this->app = $this->getApp();
    }

    private function getApp(): ?Graph
    {
        if (!$token = Cache::get('app_token')) {
            $loginUrl = config('services.azure.authority').config('services.azure.token_endpoint');
            try {
                $response = $this->client->request('POST', $loginUrl, [
                    'form_params' => [
                        'client_id' => config('services.azure.client_id'),
                        'client_secret' => config('services.azure.client_secret'),
                        'scope' => config('services.azure.scopes_app'),
                        'redirect_uri' => config('services.azure.redirect_uri'),
                        'grant_type' => 'client_credentials',
                    ],
                ]);
            } catch (GuzzleException) {
                return null;
            }

            $response = json_decode($response->getBody()->getContents(), true);
            $token = $response['access_token'];
            Cache::put('app_token', $token, 3500);
        }

        $this->graphApp->setAccessToken($token);

        return $this->graphApp;
    }

    public function getUser(string $id): GraphUser
    {
        return $this->app->createRequest('GET', "/users/$id")
            ->setReturnType(GraphUser::class)
            ->execute();
    }

    /**
     * @throws GraphException
     */
    public function getUserPhoto(string $id, int $size=240): ?string
    {
        try {
            return base64_encode(
                $this->app->createRequest('GET', "/users/$id/photos/{$size}x$size/\$value")
                ->execute()->getRawBody());
        } catch (GuzzleException $e) {
            if ($e->getCode() === 404) {
                return null;
            }
        }

        return null;
    }

    public function getUserCalendarEvents(User $user): array
    {
        $start = now()->subWeek()->startOfWeek()->toIso8601String();
        $end = now()->addWeeks(2)->endOfWeek()->toIso8601String();
        $params = [
            '$select' => 'subject,start,end,attendees,onlineMeeting',
            '$filter' => 'isCancelled eq false',
            '$orderby' => 'start/dateTime asc',
            '$top' => 100,
            'startDateTime' => $start,
            'endDateTime' => $end,
        ];
        $calendar = $this->app->createRequest('GET',
            "/users/$user->azure_id/calendar/calendarView?".http_build_query($params))
            ->setReturnType(GraphEvent::class)->execute();

        return $calendar ?: [];
    }
}
