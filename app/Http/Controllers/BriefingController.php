<?php

namespace App\Http\Controllers;

use App\Services\ChartfoxService;
use App\Services\SimbriefService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BriefingController extends Controller
{
    public function depCharts(Request $request, SimbriefService $simbriefService, ChartfoxService $chartfoxService): View
    {
        return $this->charts($request, $simbriefService, $chartfoxService, 'departure');
    }

    public function arrCharts(Request $request, SimbriefService $simbriefService, ChartfoxService $chartfoxService): View
    {
        return $this->charts($request, $simbriefService, $chartfoxService, 'arrival');
    }

    public function depBriefing(Request $request, SimbriefService $simbriefService): View
    {
        return $this->briefing($request, $simbriefService, 'departure');
    }

    public function arrBriefing(Request $request, SimbriefService $simbriefService): View
    {
        return $this->briefing($request, $simbriefService, 'arrival');
    }

    private function charts(Request $request, SimbriefService $simbriefService, ChartfoxService $chartfoxService, string $type): View
    {
        $result = $simbriefService->fetchLatestForUser(auth()->user());
        $flight = $result['data'] ?? [];

        $icao = strtoupper((string) $request->query('icao', $type === 'departure'
            ? ($flight['origin_icao'] ?? '')
            : ($flight['destination_icao'] ?? '')
        ));

        $icao = preg_match('/^[A-Z]{4}$/', $icao) ? $icao : '';
        $chartUrl = $icao ? "https://chartfox.org/{$icao}" : null;
        $interfaceUrl = $icao ? $chartfoxService->getInterfaceUrl($icao) : null;

        return view('briefings.charts', [
            'type' => $type,
            'icao' => $icao,
            'chartUrl' => $chartUrl,
            'interfaceUrl' => $interfaceUrl,
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
        $notams = $icao && is_array($raw)
            ? $simbriefService->getAerodromeNotams($raw, $icao)
            : [];

        return view('briefings.briefing', [
            'type' => $type,
            'icao' => $icao,
            'notams' => $notams,
            'simbrief' => $result,
        ]);
    }

    // Autorouter NOTAM fetching removed; SimBrief JSON is now the sole source.
}
