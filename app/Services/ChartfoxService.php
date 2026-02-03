<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class ChartfoxService
{
    public function getAirportCharts(string $icao): array
    {
        $token = config('services.chartfox.token');
        if (! $token) {
            throw new RuntimeException('ChartFox API token is missing.');
        }

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
        $token = config('services.chartfox.token');
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
}
