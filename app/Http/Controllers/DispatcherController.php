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

        $messages = DispatcherMessage::latest()->limit(20)->get();

        return view('dispatcher', [
            'bookings' => $enriched,
            'error' => $error,
            'messages' => $messages,
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

        $dispatcherMessage = DispatcherMessage::create([
            'booking_id' => $data['booking_id'],
            'pilot_id' => $data['pilot_id'],
            'discord_user_id' => $data['discord_id'],
            'message' => $data['message'],
            'sent_by' => $request->user()->id,
            'status' => 'pending',
            'booking_url' => $bookingUrl,
        ]);

        try {
            $discordBotService->sendDirectMessage($data['discord_id'], $data['message']);
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
