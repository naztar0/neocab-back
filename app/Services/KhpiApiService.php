<?php

namespace App\Services;

use App\Models\User;
use GuzzleHttp\Client;

class KhpiApiService
{
    public function __construct(private readonly Client $client)
    {
    }

    public function page1(User $user): ?array
    {
        $data = $this->getPage($user, ['page' => 1]);
        if (!$data) {
            return null;
        }
        return $data;
    }

    public function page2(User $user, int $semester): array
    {
        return $this->getPage($user, ['page' => 2, 'semester' => $semester]);
    }

    public function page3(User $user): array
    {
        return $this->getPage($user, ['page' => 3]);
    }

    public function page4(User $user, int $semester): array
    {
        return $this->getPage($user, ['page' => 4, 'semester' => $semester]);
    }

    public function page5(User $user, int $semester): array
    {
        return $this->getPage($user, ['page' => 5, 'semester' => $semester]);
    }

    private function getPage(User $user, array $params)
    {
        $response = $this->client->post(config('services.khpi.api_main'), [
            'query' => array_merge(
                $params,
                json_decode(config('services.khpi.api_params'), true),
                [
                    'email' => $user->email,
                    'pass' => $user->pass,
                ],
            ),
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }
}
