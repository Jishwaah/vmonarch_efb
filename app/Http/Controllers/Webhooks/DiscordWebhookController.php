<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\DispatcherMessage;
use App\Services\VamsysService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiscordWebhookController extends Controller
{
    public function receiveDm(Request $request, VamsysService $vamsysService): JsonResponse
    {
        $secret = config('services.discord.webhook_secret');
        $header = $request->header('X-Discord-Webhook-Token');

        if (! $secret || ! hash_equals($secret, (string) $header)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'discord_user_id' => ['required', 'string'],
            'message' => ['required', 'string', 'max:2000'],
            'author' => ['nullable', 'string', 'max:255'],
        ]);

        $bookingId = 0;
        $pilotId = 0;
        $bookingUrl = null;
        $pilotCallsign = null;

        try {
            $pilot = $vamsysService->findPilotByDiscordId($data['discord_user_id']);
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
            // Keep defaults if vAMSYS is unreachable.
        }

        DispatcherMessage::create([
            'booking_id' => $bookingId,
            'pilot_id' => $pilotId,
            'discord_user_id' => $data['discord_user_id'],
            'message' => $data['message'],
            'sent_by' => null,
            'status' => 'received',
            'booking_url' => $bookingUrl,
            'direction' => 'inbound',
            'sender_label' => $data['author'] ?? 'Pilot',
            'discord_username' => $data['author'] ?? null,
            'pilot_callsign' => $pilotCallsign,
            'received_at' => now(),
        ]);

        return response()->json(['ok' => true]);
    }
}
