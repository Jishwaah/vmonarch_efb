<?php

namespace App\Http\Controllers;

use App\Models\DispatcherMessage;
use App\Services\DiscordBotService;
use App\Services\VamsysService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DispatcherController extends Controller
{
    public function index(VamsysService $vamsysService): View
    {
        $bookings = [];
        $error = null;

        try {
            $payload = $vamsysService->getActiveBookings();
            $bookings = $payload['data'] ?? [];
        } catch (\Throwable $exception) {
            $error = $exception->getMessage();
        }

        $enriched = collect($bookings)->map(function (array $booking) use ($vamsysService) {
            $pilot = null;

            if (! empty($booking['pilot_id'])) {
                try {
                    $pilot = $vamsysService->getPilot((int) $booking['pilot_id']);
                } catch (\Throwable $exception) {
                    $pilot = null;
                }
            }

            $discordId = $pilot['discord_id'] ?? null;
            $localUser = $discordId
                ? \App\Models\User::where('discord_id', $discordId)->first()
                : null;

            $booking['pilot'] = $pilot;
            $booking['discord_id'] = $discordId;
            $booking['local_user'] = $localUser;
            $booking['efb_ready'] = $localUser && $localUser->discord_enabled && $localUser->discord_dm_enabled;
            $booking['booking_url'] = $vamsysService->formatBookingUrl((int) $booking['id']);

            return $booking;
        })->all();

        $bookingIds = collect($enriched)->pluck('id')->filter()->unique()->values();
        $messages = DispatcherMessage::query()
            ->whereIn('booking_id', $bookingIds)
            ->latest()
            ->get();

        $messagesByBooking = $messages->groupBy('booking_id')->map(function ($collection) {
            $inbound = $collection->where('direction', 'inbound')->take(5)->values();
            $outbound = $collection->where('direction', 'outbound')->take(5)->values();

            return [
                'inbound' => $inbound,
                'outbound' => $outbound,
            ];
        });

        return view('dispatcher', [
            'bookings' => $enriched,
            'error' => $error,
            'messagesByBooking' => $messagesByBooking,
        ]);
    }

    public function history(): View
    {
        $since = now()->subHours(48);

        $messages = DispatcherMessage::query()
            ->where('created_at', '>=', $since)
            ->orderByDesc('created_at')
            ->get();

        return view('dispatcher-history', [
            'messages' => $messages,
            'since' => $since,
        ]);
    }

    public function sendMessage(Request $request, VamsysService $vamsysService, DiscordBotService $discordBotService): RedirectResponse
    {
        $data = $request->validate([
            'booking_id' => ['required', 'integer'],
            'pilot_id' => ['required', 'integer'],
            'discord_id' => ['required', 'string'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $bookingUrl = $vamsysService->formatBookingUrl((int) $data['booking_id']);
        $callsign = null;

        try {
            $bookingsPayload = $vamsysService->getActiveBookings();
            $booking = collect($bookingsPayload['data'] ?? [])
                ->firstWhere('id', (int) $data['booking_id']);
            $callsign = $booking['callsign'] ?? null;
        } catch (\Throwable $exception) {
            $callsign = null;
        }

        $now = now('UTC');
        $date = strtoupper($now->format('dMy'));
        $time = $now->format('Hi').'Z';
        $aircraftReg = 'G-ZBXX';
        $callsignText = $callsign ?: 'CALLSIGN';
        $formattedMessage = "{$aircraftReg} {$callsignText} {$date} {$time}\n\n{$data['message']}";

        $dispatcherMessage = DispatcherMessage::create([
            'booking_id' => $data['booking_id'],
            'pilot_id' => $data['pilot_id'],
            'discord_user_id' => $data['discord_id'],
            'message' => $formattedMessage,
            'sent_by' => $request->user()->id,
            'status' => 'pending',
            'booking_url' => $bookingUrl,
            'direction' => 'outbound',
            'sender_label' => 'MONOPS',
        ]);

        try {
            $discordBotService->sendDirectMessage($data['discord_id'], $formattedMessage);
            $dispatcherMessage->status = 'sent';
            $dispatcherMessage->sent_at = now();
        } catch (\Throwable $exception) {
            $dispatcherMessage->status = 'failed';
            $dispatcherMessage->error_message = $exception->getMessage();
        }

        $dispatcherMessage->save();

        return redirect()->route('dispatcher')->with('status', 'Message processed.');
    }
}
