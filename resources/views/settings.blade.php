@extends('layouts.app')

@section('title', 'Settings | vMonarch EFB')
@section('header', 'Settings')

@section('content')
    <div class="card">
        <h2>Flight Data</h2>
        <form method="POST" action="{{ route('settings.update') }}" class="form">
            @csrf
            <label>
                SimBrief ID
                <input type="text" name="simbrief_id" value="{{ old('simbrief_id', auth()->user()->simbrief_id) }}" />
                <span class="hint">Used to fetch your latest OFP and display dashboard details.</span>
            </label>

            <div class="form-divider"></div>

            <label class="checkbox">
                <input type="checkbox" name="discord_enabled" value="1" {{ auth()->user()->discord_enabled ? 'checked' : '' }} />
                Enable Discord integration
            </label>
            <label class="checkbox">
                <input type="checkbox" name="discord_dm_enabled" value="1" {{ auth()->user()->discord_dm_enabled ? 'checked' : '' }} />
                Allow ACARS DMs via Discord
            </label>

            <button class="btn btn-primary" type="submit">Save settings</button>
        </form>
    </div>

    <div class="card">
        <h2>Discord Account</h2>
        @if (auth()->user()->discord_id)
            <p class="muted">Connected as {{ auth()->user()->discord_username ?? 'Discord User' }}.</p>
            <form method="POST" action="{{ route('settings.discord.disconnect') }}">
                @csrf
                <button class="btn btn-secondary" type="submit">Disconnect Discord</button>
            </form>
        @else
            <p class="muted">No Discord account linked yet.</p>
            @if ($discordOauthEnabled)
                <a class="btn btn-discord" href="{{ route('auth.discord.redirect') }}">Connect Discord</a>
            @else
                <button class="btn btn-disabled" type="button" disabled>Discord OAuth disabled</button>
            @endif
        @endif
    </div>

    <div class="card danger">
        <h2>Delete Account</h2>
        <p class="muted">
            This will permanently remove your account and settings. This cannot be undone.
        </p>
        <form method="POST" action="{{ route('settings.destroy') }}">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger" type="submit">Delete account</button>
        </form>
    </div>
@endsection
