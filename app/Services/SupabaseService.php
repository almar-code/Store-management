<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class SupabaseService
{
    private string $url;
    private string $secretKey;

    public function __construct()
    {
        $this->url = rtrim(
            config('services.supabase.url'),
            '/'
        );

        $this->secretKey = config(
            'services.supabase.secret_key'
        );

        if (
            empty($this->url) ||
            empty($this->secretKey)
        ) {
            throw new RuntimeException(
                'Supabase configuration is missing.'
            );
        }
    }

    /**
     * Get users from Supabase Auth.
     */
    public function getUsers(
        int $page = 1,
        int $perPage = 100
    ): array {
        $response = Http::withHeaders([
            'apikey' => $this->secretKey,
            'Authorization' => 'Bearer ' . $this->secretKey,
        ])->get(
            $this->url . '/auth/v1/admin/users',
            [
                'page' => $page,
                'per_page' => $perPage,
            ]
        );

        if ($response->failed()) {
            throw new RuntimeException(
                'Supabase users request failed: ' .
                $response->body()
            );
        }

        return $response->json('users', []);
    }

    /**
     * Get profiles from Supabase.
     */
    public function getProfiles(
        int $offset = 0,
        int $limit = 1000
    ): array {
        $response = Http::withHeaders([
            'apikey' => $this->secretKey,
            'Authorization' => 'Bearer ' . $this->secretKey,
        ])
        ->withHeaders([
            'Range' => "{$offset}-" . ($offset + $limit - 1),
        ])
        ->get(
            $this->url . '/rest/v1/profiles',
            [
                'select' => 'id,user_name,phone_number,avatar_url',
                'order' => 'id.asc',
            ]
        );

        if ($response->failed()) {
            throw new RuntimeException(
                'Supabase profiles request failed: ' .
                $response->body()
            );
        }

        return $response->json();
    }
}