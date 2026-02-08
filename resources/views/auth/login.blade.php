@extends('layouts.auth')

@section('title', 'Login | vMonarch EFB')

@section('content')
    <div style="text-align: center;">
        <img src="{{ asset('build/01K6G98FCG06KVEHTJQEND5EKH.png') }}" alt="vMonarch EFB" style="width: 100%; height: auto; display: block; margin: 0 auto 16px;" />
        <h1>Sign in</h1>
        <div style="display: flex; flex-direction: column; gap: 16px; align-items: center; margin: 24px 0;">
            @if ($discordOauthEnabled)
                <a class="btn btn-discord" href="{{ route('auth.discord.redirect') }}">Continue with Discord </a>
            @else
                <button class="btn btn-disabled" type="button" disabled>Discord OAuth (disabled)</button>
            @endif

            @if ($vamsysOauthEnabled)
                <button class="btn btn-secondary" type="button" disabled>vAMSYS OAuth (coming soon)</button>
            @else
                <button class="btn btn-secondary" type="button" disabled>vAMSYS OAuth (coming soon)</button>
            @endif
        </div>
    </div>
@endsection
