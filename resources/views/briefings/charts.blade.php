@extends('layouts.app')

@section('title', strtoupper($type) . ' Charts | vMonarch EFB')
@section('header', strtoupper($type) . ' Charts')

@section('content')
    <div class="card">
        <h2>{{ strtoupper($type) }} Charts</h2>
        <p class="muted">ChartFox charts for the selected aerodrome.</p>

        <form class="weather-form" method="GET">
            <label>
                ICAO
                <input type="text" name="icao" value="{{ $icao }}" placeholder="EGLL" maxlength="4" />
            </label>
            <button class="btn btn-secondary" type="submit">Load Charts</button>
            @if ($chartUrl)
                <a class="btn btn-primary" href="{{ $chartUrl }}" target="_blank" rel="noopener noreferrer">Open ChartFox</a>
            @endif
        </form>
    </div>

    <div class="card">
        @if (! $icao)
            <div class="empty-state">No ICAO found. Enter an ICAO code to load charts.</div>
        @elseif ($chartError)
            <div class="alert error">{{ $chartError }}</div>
        @endif

        @if ($interfaceUrl)
            <div class="chart-embed">
                <iframe src="{{ $interfaceUrl }}" title="ChartFox {{ $icao }}" loading="lazy"></iframe>
            </div>
        @elseif ($chartUrl)
            <div class="chart-embed">
                <iframe src="{{ $chartUrl }}" title="ChartFox {{ $icao }}" loading="lazy"></iframe>
            </div>
        @endif
    </div>

    <div class="card">
        <h2>Available Charts</h2>
        @if (empty($charts))
            <div class="empty-state">No charts returned.</div>
        @else
            <div class="chart-grid">
                @foreach ($charts as $chart)
                    <div class="chart-card">
                        <div class="chart-title">{{ $chart['name'] ?? 'Chart' }}</div>
                        <div class="chart-meta">
                            <span class="badge neutral">{{ $chart['type_key'] ?? 'Unknown' }}</span>
                            @if (! empty($chart['code']))
                                <span class="badge">{{ $chart['code'] }}</span>
                            @endif
                        </div>
                        <div class="chart-links">
                            @if (! empty($chart['view_url']))
                                <a class="btn btn-secondary" href="{{ $chart['view_url'] }}" target="_blank" rel="noopener noreferrer">Open</a>
                            @endif
                            @if (! empty($chart['id']))
                                <a class="btn btn-secondary" href="{{ ($chartUrl ?? '#') }}#{{ $chart['id'] }}" target="_blank" rel="noopener noreferrer">Open in ChartFox</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
