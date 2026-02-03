<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-title">vMonarch</div>
        <div class="brand-subtitle">EFB</div>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="sidebar-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M4,13H10V21H4V13M14,3H20V11H14V3M4,3H10V11H4V3M14,13H20V21H14V13Z"></path>
                </svg>
            </span>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('ofp') }}" class="sidebar-link {{ request()->routeIs('ofp') ? 'active' : '' }}">
            <span class="sidebar-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M6,2H14L20,8V22H6V2M14,3.5V9H19.5L14,3.5M8,13H16V15H8V13M8,17H16V19H8V17"></path>
                </svg>
            </span>
            <span>OFP</span>
        </a>
        <a href="{{ route('performance') }}" class="sidebar-link {{ request()->routeIs('performance') ? 'active' : '' }}">
            <span class="sidebar-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M12,3A9,9 0 0,1 21,12C21,16.97 16.97,21 12,21C7.03,21 3,16.97 3,12A9,9 0 0,1 12,3M12,5A7,7 0 0,0 5,12C5,15.87 8.13,19 12,19C15.87,19 19,15.87 19,12A7,7 0 0,0 12,5M13,7V12.41L16.59,16L15,17.59L11,13.59V7H13Z"></path>
                </svg>
            </span>
            <span>Performance</span>
        </a>
        <a href="{{ route('weather') }}" class="sidebar-link {{ request()->routeIs('weather') ? 'active' : '' }}">
            <span class="sidebar-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M6,19H17.5A4.5,4.5 0 0,0 17.5,10A6,6 0 0,0 6.26,8.74A4.5,4.5 0 0,0 6,19M6,21A6.5,6.5 0 0,1 5.32,8.56A8,8 0 0,1 19.5,9.5A6.5,6.5 0 0,1 17.5,21H6Z"></path>
                </svg>
            </span>
            <span>Weather</span>
        </a>
        <div class="sidebar-group {{ request()->routeIs('briefing.dep.*') ? 'open' : '' }}" data-sidebar-group>
            <button class="sidebar-group-toggle" type="button" data-sidebar-toggle>
                <span class="sidebar-link">
                    <span class="sidebar-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M21,16V14L13,9V3.5A1.5,1.5 0 0,0 11.5,2A1.5,1.5 0 0,0 10,3.5V9L2,14V16L10,13.5V19L8,20.5V22L11.5,21L15,22V20.5L13,19V13.5L21,16Z"></path>
                        </svg>
                    </span>
                    <span>Departure</span>
                </span>
                <span class="sidebar-chevron" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path d="M9.29,6.71A1,1 0 0,1 10.7,6.71L15.29,11.29C15.68,11.68 15.68,12.31 15.29,12.71L10.7,17.29A1,1 0 0,1 9.29,17.29A1,1 0 0,1 9.29,15.88L13.17,12L9.29,8.12A1,1 0 0,1 9.29,6.71Z"></path>
                    </svg>
                </span>
            </button>
            <div class="sidebar-subnav" data-sidebar-subnav>
                <a href="{{ route('briefing.dep.charts') }}" class="sidebar-sub {{ request()->routeIs('briefing.dep.charts') ? 'active' : '' }}">
                    <span class="sidebar-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M3,6H21V8H3V6M3,10H21V12H3V10M3,14H15V16H3V14M3,18H15V20H3V18Z"></path>
                        </svg>
                    </span>
                    <span>Charts</span>
                </a>
                <a href="{{ route('briefing.dep.briefing') }}" class="sidebar-sub {{ request()->routeIs('briefing.dep.briefing') ? 'active' : '' }}">
                    <span class="sidebar-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M6,2H14L20,8V22H6V2M14,3.5V9H19.5L14,3.5M8,13H16V15H8V13M8,17H14V19H8V17"></path>
                        </svg>
                    </span>
                    <span>Briefing</span>
                </a>
            </div>
        </div>
        <div class="sidebar-group {{ request()->routeIs('briefing.arr.*') ? 'open' : '' }}" data-sidebar-group>
            <button class="sidebar-group-toggle" type="button" data-sidebar-toggle>
                <span class="sidebar-link">
                    <span class="sidebar-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M21,16V14L13,9V3.5A1.5,1.5 0 0,0 11.5,2A1.5,1.5 0 0,0 10,3.5V9L2,14V16L10,13.5V19L8,20.5V22L11.5,21L15,22V20.5L13,19V13.5L21,16Z"></path>
                        </svg>
                    </span>
                    <span>Arrival</span>
                </span>
                <span class="sidebar-chevron" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path d="M9.29,6.71A1,1 0 0,1 10.7,6.71L15.29,11.29C15.68,11.68 15.68,12.31 15.29,12.71L10.7,17.29A1,1 0 0,1 9.29,17.29A1,1 0 0,1 9.29,15.88L13.17,12L9.29,8.12A1,1 0 0,1 9.29,6.71Z"></path>
                    </svg>
                </span>
            </button>
            <div class="sidebar-subnav" data-sidebar-subnav>
                <a href="{{ route('briefing.arr.charts') }}" class="sidebar-sub {{ request()->routeIs('briefing.arr.charts') ? 'active' : '' }}">
                    <span class="sidebar-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M3,6H21V8H3V6M3,10H21V12H3V10M3,14H15V16H3V14M3,18H15V20H3V18Z"></path>
                        </svg>
                    </span>
                    <span>Charts</span>
                </a>
                <a href="{{ route('briefing.arr.briefing') }}" class="sidebar-sub {{ request()->routeIs('briefing.arr.briefing') ? 'active' : '' }}">
                    <span class="sidebar-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M6,2H14L20,8V22H6V2M14,3.5V9H19.5L14,3.5M8,13H16V15H8V13M8,17H14V19H8V17"></path>
                        </svg>
                    </span>
                    <span>Briefing</span>
                </a>
            </div>
        </div>
        <a href="{{ route('acars') }}" class="sidebar-link {{ request()->routeIs('acars') ? 'active' : '' }}">
            <span class="sidebar-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M4,4H20A2,2 0 0,1 22,6V15A2,2 0 0,1 20,17H7L4,20V6A2,2 0 0,1 6,4M7,8H17V10H7V8M7,12H14V14H7V12Z"></path>
                </svg>
            </span>
            <span>ACARS</span>
        </a>
        @if (auth()->user()->isStaff())
            <a href="{{ route('dispatcher') }}" class="sidebar-link {{ request()->routeIs('dispatcher') ? 'active' : '' }}">
                <span class="sidebar-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path d="M12,2A10,10 0 0,1 22,12H20A8,8 0 0,0 4,12H2A10,10 0 0,1 12,2M12,6A6,6 0 0,1 18,12H16A4,4 0 0,0 8,12H6A6,6 0 0,1 12,6M12,10A2,2 0 0,1 14,12H10A2,2 0 0,1 12,10M12,16A4,4 0 0,1 16,20H8A4,4 0 0,1 12,16Z"></path>
                    </svg>
                </span>
                <span>Dispatcher</span>
            </a>
        @endif
        <a href="{{ route('settings') }}" class="sidebar-link {{ request()->routeIs('settings') ? 'active' : '' }}">
            <span class="sidebar-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M12,8A4,4 0 1,1 8,12A4,4 0 0,1 12,8M4,12A8,8 0 0,1 20,12H18A6,6 0 0,0 6,12H4M12,2A10,10 0 0,1 22,12H20A8,8 0 0,0 4,12H2A10,10 0 0,1 12,2Z"></path>
                </svg>
            </span>
            <span>Settings</span>
        </a>
    </nav>
</aside>
