<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class DiscordBotService
{
    public function sendDirectMessage(string $discordUserId, string $message, string $senderLabel = 'MONOPS'): void
    {
        $token = config('services.discord.bot_token');

        if (! $token) {
            throw new RuntimeException('Discord bot token is missing.');
        }

        $channelResponse = Http::withHeaders([
            'Authorization' => 'Bot '.$token,
        ])->post('https://discord.com/api/v10/users/@me/channels', [
            'recipient_id' => $discordUserId,
        ]);

        if (! $channelResponse->ok()) {
            throw new RuntimeException('Failed to open Discord DM channel.');
        }

        $channelId = $channelResponse->json('id');
        if (! $channelId) {
            throw new RuntimeException('Discord DM channel ID missing.');
        }

        $payload = [
            'embeds' => [
                [
                    'title' => "Message from {$senderLabel}",
                    'description' => $message,
                    'color' => 0x3B82F6,
                ],
            ],
        ];

        $messageResponse = Http::withHeaders([
            'Authorization' => 'Bot '.$token,
        ])->post("https://discord.com/api/v10/channels/{$channelId}/messages", $payload);

        if (! $messageResponse->ok()) {
            throw new RuntimeException('Failed to send Discord DM.');
        }
    }
}
