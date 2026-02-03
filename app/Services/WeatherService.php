<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WeatherService
{
    public function fetchForAirport(string $icao): array
    {
        $icao = strtoupper(trim($icao));
        if (! preg_match('/^[A-Z0-9]{4}$/', $icao)) {
            return [
                'status' => 'invalid',
                'message' => 'Invalid ICAO identifier.',
                'icao' => $icao,
            ];
        }

        $baseUrl = rtrim((string) config('services.weather.base_url'), '/');

        $metarResponse = Http::timeout(10)
            ->acceptJson()
            ->get($baseUrl.'/metar', [
                'ids' => $icao,
                'format' => 'json',
            ]);

        $tafResponse = Http::timeout(10)
            ->acceptJson()
            ->get($baseUrl.'/taf', [
                'ids' => $icao,
                'format' => 'json',
            ]);

        if (! $metarResponse->ok()) {
            return [
                'status' => 'error',
                'message' => 'Unable to fetch METAR data.',
                'icao' => $icao,
            ];
        }

        $metarPayload = $metarResponse->json();
        $tafPayload = $tafResponse->ok() ? $tafResponse->json() : [];

        $metar = is_array($metarPayload) ? ($metarPayload[0] ?? null) : null;
        $taf = is_array($tafPayload) ? ($tafPayload[0] ?? null) : null;

        if (! $metar) {
            return [
                'status' => 'empty',
                'message' => 'No METAR data found.',
                'icao' => $icao,
            ];
        }

        $clouds = is_array(data_get($metar, 'clouds')) ? data_get($metar, 'clouds') : [];
        $rawMetar = data_get($metar, 'rawOb');
        $visibility = data_get($metar, 'visib');
        $visibilityDisplay = $this->formatVisibility($rawMetar, $visibility);

        return [
            'status' => 'ok',
            'icao' => $icao,
            'name' => data_get($metar, 'name'),
            'raw_metar' => $rawMetar,
            'raw_taf' => data_get($taf, 'rawTAF'),
            'temperature_c' => data_get($metar, 'temp'),
            'dewpoint_c' => data_get($metar, 'dewp'),
            'visibility' => $visibilityDisplay,
            'wind_dir' => data_get($metar, 'wdir'),
            'wind_speed' => data_get($metar, 'wspd'),
            'wind_gust' => data_get($metar, 'wgst'),
            'qnh_hpa' => data_get($metar, 'altim') ?? data_get($metar, 'slp'),
            'wx_string' => data_get($metar, 'wxString'),
            'clouds' => $clouds,
            'flight_category' => data_get($metar, 'fltCat'),
            'icon' => $this->resolveIcon(data_get($metar, 'wxString'), $clouds),
        ];
    }

    private function resolveIcon(?string $wxString, array $clouds): string
    {
        $wxString = strtoupper((string) $wxString);

        if (Str::contains($wxString, ['TS', 'SQ'])) {
            return 'storm';
        }

        if (Str::contains($wxString, ['SN', 'SG', 'IC', 'PL'])) {
            return 'snow';
        }

        if (Str::contains($wxString, ['RA', 'DZ'])) {
            return 'rain';
        }

        if (Str::contains($wxString, ['FG', 'BR', 'HZ'])) {
            return 'fog';
        }

        foreach ($clouds as $cloud) {
            $cover = strtoupper((string) data_get($cloud, 'cover'));
            if (in_array($cover, ['BKN', 'OVC', 'OVX'], true)) {
                return 'cloud';
            }
        }

        return 'clear';
    }

    private function formatVisibility(?string $rawMetar, $visibility): ?array
    {
        if ($visibility === null) {
            return null;
        }

        $raw = strtoupper((string) $rawMetar);
        $isStatute = str_contains($raw, 'SM');

        if ($isStatute) {
            return [
                'value' => $visibility,
                'unit' => 'SM',
            ];
        }

        // METAR 9999 means 10km or more.
        if (preg_match('/\b9999\b/', $raw)) {
            return [
                'value' => '10+',
                'unit' => 'km',
            ];
        }

        if (is_numeric($visibility)) {
            $meters = round(((float) $visibility) * 1609.34);

            return $this->formatMeters($meters);
        }

        if (is_string($visibility) && is_numeric($visibility)) {
            return $this->formatMeters((int) $visibility);
        }

        return [
            'value' => (string) $visibility,
            'unit' => 'm',
        ];
    }

    private function formatMeters(int $meters): array
    {
        if ($meters >= 1000) {
            return [
                'value' => number_format($meters / 1000, 1),
                'unit' => 'km',
            ];
        }

        return [
            'value' => (string) $meters,
            'unit' => 'm',
        ];
    }
}
