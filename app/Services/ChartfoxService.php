<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ChartfoxService
{
    public function getAirportCharts(string $icao): array
    {
        $token = $this->getAccessToken();

        $baseUrl = rtrim((string) config('services.chartfox.base_url', 'https://api.chartfox.org'), '/');
        $icao = strtoupper($icao);

        $response = Http::withToken($token)
            ->acceptJson()
            ->get("{$baseUrl}/v2/airports/{$icao}/charts");

        if (! $response->ok()) {
            throw new RuntimeException('ChartFox API request failed.');
        }

        $payload = $response->json();
        if (! is_array($payload)) {
            throw new RuntimeException('ChartFox API returned an unexpected response.');
        }

        return $payload['data'] ?? [];
    }

    public function getInterfaceUrl(string $icao, bool $darkMode = true): ?string
    {
        $token = $this->getAccessToken(false);
        if (! $token) {
            return null;
        }

        $baseUrl = rtrim((string) config('services.chartfox.base_url', 'https://api.chartfox.org'), '/');
        $icao = strtoupper($icao);
        $query = http_build_query([
            'token' => $token,
            'darkMode' => $darkMode ? 'true' : 'false',
        ]);

        return "{$baseUrl}/v2/interfaces/airport/{$icao}?{$query}";
    }

    private function getAccessToken(bool $required = true): ?string
    {
        $pat = config('services.chartfox.token');
        if ($pat) {
            return $pat;
        }

        $clientId = config('services.chartfox.client_id');
        $clientSecret = config('services.chartfox.client_secret');
        $tokenUrl = config('services.chartfox.token_url', 'https://api.chartfox.org/oauth/token');

        if (! $clientId || ! $clientSecret) {
            if ($required) {
                throw new RuntimeException('ChartFox API token or OAuth client credentials are missing.');
            }

            return null;
        }

        return Cache::remember('chartfox.access_token', now()->addMinutes(50), function () use ($clientId, $clientSecret, $tokenUrl) {
            $response = Http::asForm()->post($tokenUrl, [
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]);

            if (! $response->ok()) {
                throw new RuntimeException('ChartFox OAuth token request failed.');
            }

            $token = $response->json('access_token');
            if (! is_string($token) || $token === '') {
                throw new RuntimeException('ChartFox OAuth token response was invalid.');
            }

            $expiresIn = (int) $response->json('expires_in', 3600);
            if ($expiresIn > 120) {
                Cache::put('chartfox.access_token', $token, now()->addSeconds($expiresIn - 60));
            }

            return $token;
        });
    }
}
