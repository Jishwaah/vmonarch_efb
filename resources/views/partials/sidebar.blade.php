<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-title">vMonarch</div>
        <div class="brand-subtitle">EFB</div>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('ofp') }}" class="{{ request()->routeIs('ofp') ? 'active' : '' }}">OFP</a>
        <a href="{{ route('performance') }}" class="{{ request()->routeIs('performance') ? 'active' : '' }}">Performance</a>
        <a href="{{ route('weather') }}" class="{{ request()->routeIs('weather') ? 'active' : '' }}">Weather</a>
        <a href="{{ route('acars') }}" class="{{ request()->routeIs('acars') ? 'active' : '' }}">ACARS</a>
        @if (auth()->user()->isStaff())
            <a href="{{ route('dispatcher') }}" class="{{ request()->routeIs('dispatcher') ? 'active' : '' }}">Dispatcher</a>
        @endif
        <a href="{{ route('settings') }}" class="{{ request()->routeIs('settings') ? 'active' : '' }}">Settings</a>
    </nav>
</aside>
