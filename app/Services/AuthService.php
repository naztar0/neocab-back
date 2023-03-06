<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class AuthService
{
    public function __construct()
    {
    }

    public function checkAuthValidationRequest(array $credentials): bool
    {
        $cacheKey = 'auth:'. $credentials['email'] . ':' . $credentials['pass'];

        if (!Cache::has($cacheKey)) {
            Cache::put($cacheKey, 0, 60);
            return true;
        } else {
            $result = Cache::get($cacheKey) == 0;
            if (!$result) {
                Cache::forget($cacheKey);
            }
            return $result;
        }
    }
}
