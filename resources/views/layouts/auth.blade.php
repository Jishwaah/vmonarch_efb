<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>@yield('title', 'vMonarch EFB')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="auth-body">
        <div class="auth-shell">
            <div class="auth-card">
                <div class="auth-brand">
                    <div class="brand-title">vMonarch EFB</div>
                    <div class="brand-subtitle">Pilot portal</div>
                </div>
                <div class="utc-clock compact" data-utc-clock>
                    <span class="utc-label">UTC</span>
                    <span class="utc-time" data-utc-time>--:--:--</span>
                </div>
                @include('partials.flash')
                @yield('content')
            </div>
        </div>
    </body>
</html>
