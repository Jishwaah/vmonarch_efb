@php
    $data = is_array($weather) ? $weather : [];
    $status = $data['status'] ?? 'empty';
    $icon = $data['icon'] ?? 'clear';
    $windDir = $data['wind_dir'] ?? null;
    $windSpeed = $data['wind_speed'] ?? null;
    $windGust = $data['wind_gust'] ?? null;
    $visibility = $data['visibility_sm'] ?? null;
    $qnh = $data['qnh_hpa'] ?? null;
    $temp = $data['temperature_c'] ?? null;
    $dew = $data['dewpoint_c'] ?? null;
    $clouds = $data['clouds'] ?? [];
    $rawMetar = $data['raw_metar'] ?? null;
    $rawTaf = $data['raw_taf'] ?? null;
    $name = $data['name'] ?? null;
    $flightCategory = $data['flight_category'] ?? null;
@endphp

<div class="card weather-card">
    <div class="weather-header">
        <div>
            <div class="weather-title">{{ $title }}</div>
            <div class="weather-airport">
                <span class="icao">{{ $airport ?: '----' }}</span>
                @if ($name)
                    <span class="muted">{{ $name }}</span>
                @endif
            </div>
        </div>
        <div class="weather-icon icon-{{ $icon }}">
            @switch($icon)
                @case('storm')
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M7 18h4l-2 4 7-7h-4l2-5z" />
                        <path d="M6 16a6 6 0 0 1 11.5-2.2A4 4 0 0 1 17 22H7a5 5 0 0 1-1-10z" />
                    </svg>
                    @break
                @case('snow')
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 2v20M4 6l16 12M20 6L4 18M4 18l16-12" />
                    </svg>
                    @break
                @case('rain')
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M7 15a5 5 0 0 1 9.7-1.5A4 4 0 0 1 16 21H7a4 4 0 0 1 0-8z" />
                        <path d="M8 22l-2 3M12 22l-2 3M16 22l-2 3" />
                    </svg>
                    @break
                @case('fog')
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M3 15h18M5 19h14M4 11h16" />
                    </svg>
                    @break
                @case('cloud')
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M7 18a5 5 0 0 1 9.7-1.5A4 4 0 0 1 16 22H7a4 4 0 0 1 0-8z" />
                    </svg>
                    @break
                @default
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="5" />
                        <path d="M12 1v4M12 19v4M1 12h4M19 12h4M4.2 4.2l2.8 2.8M17 17l2.8 2.8M19.8 4.2L17 7M7 17l-2.8 2.8" />
                    </svg>
            @endswitch
        </div>
    </div>

    @if ($status !== 'ok')
        <p class="muted">{{ $data['message'] ?? 'No weather data available.' }}</p>
    @else
        <div class="weather-metrics">
            <div class="metric">
                <div class="label">Temp</div>
                <div class="value">{{ $temp !== null ? $temp.'°C' : '—' }}</div>
            </div>
            <div class="metric">
                <div class="label">Dewpoint</div>
                <div class="value">{{ $dew !== null ? $dew.'°C' : '—' }}</div>
            </div>
            <div class="metric">
                <div class="label">Visibility</div>
                <div class="value">{{ $visibility !== null ? $visibility.' SM' : '—' }}</div>
            </div>
            <div class="metric">
                <div class="label">QNH</div>
                <div class="value">{{ $qnh !== null ? $qnh.' hPa' : '—' }}</div>
            </div>
        </div>

        <div class="weather-wind">
            <div class="wind-arrow" style="{{ is_numeric($windDir) ? 'transform: rotate('.$windDir.'deg);' : '' }}">
                <span class="arrow-shaft"></span>
                <span class="arrow-head"></span>
            </div>
            <div>
                <div class="label">Wind</div>
                <div class="value">
                    @if ($windDir !== null)
                        {{ $windDir }}°
                    @else
                        VRB
                    @endif
                    {{ $windSpeed !== null ? $windSpeed.'kt' : '' }}
                    @if ($windGust)
                        G{{ $windGust }}
                    @endif
                </div>
            </div>
            @if ($flightCategory)
                <div class="flight-category">{{ $flightCategory }}</div>
            @endif
        </div>

        <div class="weather-clouds">
            <div class="label">Clouds</div>
            <div class="cloud-list">
                @if (count($clouds))
                    @foreach ($clouds as $cloud)
                        <span class="cloud-chip">
                            {{ data_get($cloud, 'cover') }} {{ data_get($cloud, 'base') ? data_get($cloud, 'base').'ft' : '' }}
                        </span>
                    @endforeach
                @else
                    <span class="muted">None reported</span>
                @endif
            </div>
        </div>

        <div class="weather-raw">
            <div class="raw-block">
                <div class="label">METAR</div>
                <div class="raw-text">{{ $rawMetar ?? '—' }}</div>
            </div>
            <div class="raw-block">
                <div class="label">TAF</div>
                <div class="raw-text">{{ $rawTaf ?? '—' }}</div>
            </div>
        </div>
    @endif
</div>
