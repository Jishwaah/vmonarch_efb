<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class DiscordAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        if (! config('services.discord.enabled')) {
            abort(404);
        }

        return Socialite::driver('discord')
            ->scopes(['identify', 'email', 'guilds.members.read'])
            ->stateless()
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        if (! config('services.discord.enabled')) {
            abort(404);
        }

        $discordUser = Socialite::driver('discord')->stateless()->user();
        $discordId = $discordUser->getId();
        $email = $discordUser->getEmail();
        $accessToken = $discordUser->token;

        $guildId = config('services.discord.guild_id');
        $roles = [];

        if ($guildId && $accessToken) {
            $memberResponse = Http::withToken($accessToken)
                ->get("https://discord.com/api/users/@me/guilds/{$guildId}/member");

            if ($memberResponse->ok()) {
                $roles = $memberResponse->json('roles', []);
            }
        }

        $user = User::where('discord_id', $discordId)->first();

        if (! $user && $email) {
            $user = User::where('email', $email)->first();
        }

        if (! $user && ! $email) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Discord did not provide an email address. Please log in with email and password first.']);
        }

        if (! $user) {
            $user = User::create([
                'name' => $discordUser->getName() ?: $discordUser->getNickname() ?: 'Discord Pilot',
                'email' => $email,
                'password' => Str::password(32),
            ]);
        }

        $user->forceFill([
            'discord_id' => $discordId,
            'discord_username' => $discordUser->getNickname() ?: $discordUser->getName(),
            'discord_avatar' => $discordUser->getAvatar(),
            'discord_enabled' => true,
            'discord_roles' => $roles,
            'discord_guild_id' => $guildId,
            'discord_roles_synced_at' => now(),
        ])->save();

        if (! $user->isPilot()) {
            Auth::logout();

            return redirect()->route('login')
                ->withErrors(['email' => 'You do not have the required Discord role to access this portal.']);
        }

        Auth::login($user);

        request()->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
