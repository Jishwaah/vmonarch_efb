<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('settings', [
            'discordOauthEnabled' => config('services.discord.enabled', false),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'simbrief_id' => ['nullable', 'string', 'max:255'],
            'discord_enabled' => ['nullable', 'boolean'],
            'discord_dm_enabled' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        $user->simbrief_id = $data['simbrief_id'] ?? null;
        $user->discord_enabled = $request->boolean('discord_enabled');
        $user->discord_dm_enabled = $request->boolean('discord_dm_enabled');
        $user->save();

        return redirect()->route('settings')->with('status', 'Settings updated.');
    }

    public function disconnectDiscord(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->discord_id = null;
        $user->discord_username = null;
        $user->discord_avatar = null;
        $user->discord_enabled = false;
        $user->discord_dm_enabled = false;
        $user->save();

        return redirect()->route('settings')->with('status', 'Discord account disconnected.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->delete();

        return redirect()->route('login')->with('status', 'Account deleted.');
    }
}
