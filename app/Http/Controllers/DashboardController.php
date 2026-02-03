<?php

namespace App\Http\Controllers;

use App\Services\SimbriefService;
use App\Services\VamsysService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(SimbriefService $simbriefService, VamsysService $vamsysService): View
    {
        $result = $simbriefService->fetchLatestForUser(auth()->user());
        $booking = null;

        $discordId = auth()->user()->discord_id;
        if ($discordId) {
            try {
                $pilot = $vamsysService->findPilotByDiscordId($discordId);
                if ($pilot && ! empty($pilot['id'])) {
                    $bookingsPayload = $vamsysService->getActiveBookings();
                    $booking = collect($bookingsPayload['data'] ?? [])
                        ->firstWhere('pilot_id', $pilot['id']);
                    if ($booking) {
                        $booking['booking_url'] = $vamsysService->formatBookingUrl((int) $booking['id']);
                    }
                }
            } catch (\Throwable $exception) {
                $booking = null;
            }
        }

        return view('dashboard', [
            'simbrief' => $result,
            'flight' => $result['data'] ?? null,
            'booking' => $booking,
        ]);
    }

    public function refreshSimbrief(SimbriefService $simbriefService): RedirectResponse
    {
        $simbriefService->fetchLatestForUser(auth()->user(), true);

        return redirect()->route('dashboard')->with('status', 'SimBrief data refreshed.');
    }
}
