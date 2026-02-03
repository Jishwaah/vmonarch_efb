@extends('layouts.auth')

@section('title', 'Login | vMonarch EFB')

@section('content')
    <h1>Sign in</h1>
    <form method="POST" action="{{ route('login.attempt') }}" class="form">
        @csrf
        <label>
            Email
            <input type="email" name="email" value="{{ old('email') }}" required autofocus />
        </label>
        <label>
            Password
            <input type="password" name="password" required />
        </label>
        <label class="checkbox">
            <input type="checkbox" name="remember" />
            Remember me
        </label>
        <button class="btn btn-primary" type="submit">Log in</button>
    </form>

    <div class="auth-divider">or</div>

    @if ($discordOauthEnabled)
        <a class="btn btn-discord" href="{{ route('auth.discord.redirect') }}">Continue with Discord (role-based)</a>
    @else
        <button class="btn btn-disabled" type="button" disabled>Discord OAuth (disabled)</button>
    @endif

    @if ($vamsysOauthEnabled)
        <button class="btn btn-secondary" type="button" disabled>vAMSYS OAuth (coming soon)</button>
    @else
        <button class="btn btn-secondary" type="button" disabled>vAMSYS OAuth (coming soon)</button>
    @endif

    <p class="auth-footer">
        Need an account?
        <a href="{{ route('register') }}">Register here</a>
    </p>
@endsection
