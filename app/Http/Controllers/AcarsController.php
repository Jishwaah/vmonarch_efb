<?php

namespace App\Http\Controllers;

use App\Models\DispatcherMessage;
use App\Services\VamsysService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcarsController extends Controller
{
    public function index(Request $request, VamsysService $vamsysService): View
    {
        $user = $request->user();
        $booking = null;
        $pilot = null;

        if ($user->discord_id) {
            try {
                $pilot = $vamsysService->findPilotByDiscordId($user->discord_id);
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

        $messages = DispatcherMessage::query()
            ->when($user->discord_id, function ($query) use ($user) {
                $query->where('discord_user_id', $user->discord_id);
            })
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return view('acars', [
            'messages' => $messages,
            'booking' => $booking,
            'pilot' => $pilot,
        ]);
    }

    public function send(Request $request, VamsysService $vamsysService): RedirectResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $user = $request->user();
        if (! $user->discord_id) {
            return redirect()->route('acars')->withErrors(['message' => 'Connect your Discord account first.']);
        }

        $bookingId = 0;
        $pilotId = 0;
        $bookingUrl = null;
        $pilotCallsign = null;

        try {
            $pilot = $vamsysService->findPilotByDiscordId($user->discord_id);
            if ($pilot && ! empty($pilot['id'])) {
                $pilotId = (int) $pilot['id'];
                $pilotCallsign = $pilot['username'] ?? null;
                $bookingsPayload = $vamsysService->getActiveBookings();
                $booking = collect($bookingsPayload['data'] ?? [])
                    ->firstWhere('pilot_id', $pilotId);
                if ($booking) {
                    $bookingId = (int) $booking['id'];
                    $bookingUrl = $vamsysService->formatBookingUrl($bookingId);
                }
            }
        } catch (\Throwable $exception) {
            // No-op: allow message to be stored without booking context.
        }

        DispatcherMessage::create([
            'booking_id' => $bookingId,
            'pilot_id' => $pilotId,
            'discord_user_id' => $user->discord_id,
            'message' => $data['message'],
            'sent_by' => null,
            'status' => 'received',
            'booking_url' => $bookingUrl,
            'direction' => 'inbound',
            'sender_label' => $user->name,
            'discord_username' => $user->discord_username,
            'pilot_callsign' => $pilotCallsign,
            'received_at' => now(),
        ]);

        return redirect()->route('acars')->with('status', 'Message sent to dispatch.');
    }
}
