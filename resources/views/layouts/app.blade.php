<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>@yield('title', 'vMonarch EFB')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="app-shell">
            @include('partials.sidebar')

            <main class="app-main">
                <header class="app-header">
                    <div>
                        <div class="app-title">@yield('header', 'Dashboard')</div>
                        <div class="app-subtitle">Electronic Flight Bag</div>
                    </div>
                    <div class="utc-clock" data-utc-clock>
                        <span class="utc-label">UTC</span>
                        <span class="utc-time" data-utc-time>--:--:--</span>
                    </div>
                    <div class="app-user">
                        <span>{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-secondary" type="submit">Log out</button>
                        </form>
                    </div>
                </header>

                @include('partials.flash')

                <section class="app-content">
                    @yield('content')
                </section>
            </main>
        </div>
    </body>
</html>
