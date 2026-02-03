@extends('layouts.auth')

@section('title', 'Register | vMonarch EFB')

@section('content')
    <h1>Create account</h1>
    <form method="POST" action="{{ route('register.store') }}" class="form">
        @csrf
        <label>
            Name
            <input type="text" name="name" value="{{ old('name') }}" required />
        </label>
        <label>
            Email
            <input type="email" name="email" value="{{ old('email') }}" required />
        </label>
        <label>
            SimBrief ID
            <input type="text" name="simbrief_id" value="{{ old('simbrief_id') }}" placeholder="Optional" />
            <span class="hint">Used to fetch your latest OFP automatically.</span>
        </label>
        <label>
            Password
            <input type="password" name="password" required />
        </label>
        <label>
            Confirm password
            <input type="password" name="password_confirmation" required />
        </label>
        <button class="btn btn-primary" type="submit">Create account</button>
    </form>

    <div class="auth-divider">or</div>

    @if ($discordOauthEnabled)
        <a class="btn btn-discord" href="{{ route('auth.discord.redirect') }}">Continue with Discord (role-based)</a>
    @else
        <button class="btn btn-disabled" type="button" disabled>Discord OAuth (disabled)</button>
    @endif

    <p class="auth-footer">
        Already registered?
        <a href="{{ route('login') }}">Sign in</a>
    </p>
@endsection
