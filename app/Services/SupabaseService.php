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

        if (empty($this->url) || empty($this->secretKey)) {
            throw new RuntimeException(
                'Supabase configuration is missing.'
            );
        }
    }

    public function getUserById(string $userId): ?array
    {
        $response = Http::withHeaders([
            'apikey' => $this->secretKey,
            'Authorization' => 'Bearer ' . $this->secretKey,
        ])->get(
            $this->url . '/auth/v1/admin/users/' . $userId
        );

        if ($response->status() === 404) {
            return null;
        }

        if ($response->failed()) {
            throw new RuntimeException(
                'Supabase user request failed: ' .
                $response->body()
            );
        }

        return $response->json();
    }
}