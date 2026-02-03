<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class VamsysService
{
    public function getActiveBookings(int $perPage = 50): array
    {
        return $this->get('/bookings', [
            'filter' => ['status' => 'current'],
            'page' => ['size' => $perPage],
            'sort' => '-id',
        ]);
    }

    public function getPilot(int $pilotId): ?array
    {
        $cacheKey = "vamsys.pilot.{$pilotId}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($pilotId) {
            $response = $this->get("/pilots/{$pilotId}");

            return $response['data'] ?? null;
        });
    }

    public function findPilotByDiscordId(string $discordId): ?array
    {
        $response = $this->get('/pilots', [
            'filter' => ['discord_id' => $discordId],
            'page' => ['size' => 1],
        ]);

        return $response['data'][0] ?? null;
    }

    public function formatBookingUrl(int $bookingId): string
    {
        $template = config('services.vamsys.web_booking_url');

        return str_replace('{id}', (string) $bookingId, $template);
    }

    private function get(string $path, array $query = []): array
    {
        $token = $this->getAccessToken();
        $baseUrl = rtrim((string) config('services.vamsys.base_url'), '/');

        $response = Http::withToken($token)
            ->acceptJson()
            ->get($baseUrl.$path, $query);

        if (! $response->ok()) {
            throw new RuntimeException('vAMSYS API request failed.');
        }

        return $response->json();
    }

    private function getAccessToken(): string
    {
        return Cache::remember('vamsys.access_token', now()->addDays(6), function () {
            $clientId = config('services.vamsys.client_id');
            $clientSecret = config('services.vamsys.client_secret');
            $tokenUrl = config('services.vamsys.token_url', 'https://vamsys.io/oauth/token');

            if (! $clientId || ! $clientSecret) {
                throw new RuntimeException('vAMSYS client ID/secret are missing.');
            }

            $response = Http::asForm()->post($tokenUrl, [
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'scope' => '*',
            ]);

            if (! $response->ok()) {
                throw new RuntimeException('Unable to obtain vAMSYS access token.');
            }

            $token = $response->json('access_token');
            if (! is_string($token) || ! Str::length($token)) {
                throw new RuntimeException('Invalid vAMSYS access token response.');
            }

            return $token;
        });
    }
}
