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
        <div class="sidebar-group {{ request()->routeIs('briefing.dep.*') ? 'open' : '' }}" data-sidebar-group>
            <button class="sidebar-group-toggle" type="button" data-sidebar-toggle>
                <span>DEP BRIEF</span>
                <span class="sidebar-chevron">▸</span>
            </button>
            <div class="sidebar-subnav" data-sidebar-subnav>
                <a href="{{ route('briefing.dep.charts') }}" class="sidebar-sub {{ request()->routeIs('briefing.dep.charts') ? 'active' : '' }}">Charts</a>
                <a href="{{ route('briefing.dep.briefing') }}" class="sidebar-sub {{ request()->routeIs('briefing.dep.briefing') ? 'active' : '' }}">Briefing</a>
            </div>
        </div>
        <div class="sidebar-group {{ request()->routeIs('briefing.arr.*') ? 'open' : '' }}" data-sidebar-group>
            <button class="sidebar-group-toggle" type="button" data-sidebar-toggle>
                <span>ARR BRIEF</span>
                <span class="sidebar-chevron">▸</span>
            </button>
            <div class="sidebar-subnav" data-sidebar-subnav>
                <a href="{{ route('briefing.arr.charts') }}" class="sidebar-sub {{ request()->routeIs('briefing.arr.charts') ? 'active' : '' }}">Charts</a>
                <a href="{{ route('briefing.arr.briefing') }}" class="sidebar-sub {{ request()->routeIs('briefing.arr.briefing') ? 'active' : '' }}">Briefing</a>
            </div>
        </div>
        <a href="{{ route('acars') }}" class="{{ request()->routeIs('acars') ? 'active' : '' }}">ACARS</a>
        @if (auth()->user()->isStaff())
            <a href="{{ route('dispatcher') }}" class="{{ request()->routeIs('dispatcher') ? 'active' : '' }}">Dispatcher</a>
        @endif
        <a href="{{ route('settings') }}" class="{{ request()->routeIs('settings') ? 'active' : '' }}">Settings</a>
    </nav>
</aside>
