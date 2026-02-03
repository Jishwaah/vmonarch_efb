<?php

namespace App\Http\Controllers;

use App\Services\SimbriefService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BriefingController extends Controller
{
    public function depCharts(Request $request, SimbriefService $simbriefService): View
    {
        return $this->charts($request, $simbriefService, 'departure');
    }

    public function arrCharts(Request $request, SimbriefService $simbriefService): View
    {
        return $this->charts($request, $simbriefService, 'arrival');
    }

    public function depBriefing(Request $request, SimbriefService $simbriefService): View
    {
        return $this->briefing($request, $simbriefService, 'departure');
    }

    public function arrBriefing(Request $request, SimbriefService $simbriefService): View
    {
        return $this->briefing($request, $simbriefService, 'arrival');
    }

    private function charts(Request $request, SimbriefService $simbriefService, string $type): View
    {
        $result = $simbriefService->fetchLatestForUser(auth()->user());
        $flight = $result['data'] ?? [];

        $icao = strtoupper((string) $request->query('icao', $type === 'departure'
            ? ($flight['origin_icao'] ?? '')
            : ($flight['destination_icao'] ?? '')
        ));

        $icao = preg_match('/^[A-Z]{4}$/', $icao) ? $icao : '';
        $chartUrl = $icao ? "https://chartfox.org/{$icao}" : null;

        return view('briefings.charts', [
            'type' => $type,
            'icao' => $icao,
            'chartUrl' => $chartUrl,
            'simbrief' => $result,
        ]);
    }

    private function briefing(Request $request, SimbriefService $simbriefService, string $type): View
    {
        $result = $simbriefService->fetchLatestForUser(auth()->user());
        $flight = $result['data'] ?? [];
        $raw = $result['raw'] ?? [];

        $icao = strtoupper((string) $request->query('icao', $type === 'departure'
            ? ($flight['origin_icao'] ?? '')
            : ($flight['destination_icao'] ?? '')
        ));

        $icao = preg_match('/^[A-Z]{4}$/', $icao) ? $icao : '';
        $notams = $icao ? $this->fetchAutorouterNotams($icao) : [];
        if (empty($notams) && $icao && is_array($raw)) {
            $notams = $simbriefService->getAerodromeNotams($raw, $icao);
        }

        return view('briefings.briefing', [
            'type' => $type,
            'icao' => $icao,
            'notams' => $notams,
            'simbrief' => $result,
        ]);
    }

    private function fetchAutorouterNotams(string $icao): array
    {
        $baseUrl = rtrim((string) config('services.autorouter.base_url', 'https://api.autorouter.aero/v1.0'), '/');

        $response = \Illuminate\Support\Facades\Http::timeout(10)
            ->acceptJson()
            ->get($baseUrl.'/notam', [
                'itemas' => json_encode([$icao]),
                'offset' => 0,
                'limit' => 10,
            ]);

        if (! $response->ok()) {
            return [];
        }

        $payload = $response->json();
        if (! is_array($payload)) {
            return [];
        }

        return $this->extractAutorouterNotams($payload);
    }

    private function extractAutorouterNotams(array $payload): array
    {
        $candidates = [];

        foreach (['notams', 'data', 'items', 'results'] as $key) {
            if (isset($payload[$key])) {
                $candidates[] = $payload[$key];
            }
        }

        $candidates[] = $payload;

        $notams = [];

        foreach ($candidates as $candidate) {
            if (is_string($candidate)) {
                $parts = preg_split('/\r\n\r\n|\n\n|\r\n|\n/', $candidate);
                foreach ($parts as $part) {
                    $text = trim($part);
                    if ($text !== '') {
                        $notams[] = $text;
                    }
                }
                continue;
            }

            if (is_array($candidate)) {
                foreach ($candidate as $item) {
                    if (is_string($item)) {
                        $text = trim($item);
                        if ($text !== '') {
                            $notams[] = $text;
                        }
                        continue;
                    }

                    if (is_array($item)) {
                        $text = $item['text']
                            ?? $item['raw']
                            ?? $item['message']
                            ?? $item['notam']
                            ?? null;
                        if (is_string($text) && trim($text) !== '') {
                            $notams[] = trim($text);
                        }
                    }
                }
            }
        }

        return array_values(array_unique($notams));
    }
}
