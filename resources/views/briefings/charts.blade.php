@extends('layouts.app')

@section('title', strtoupper($type) . ' Charts | vMonarch EFB')
@section('header', strtoupper($type) . ' Charts')

@section('content')
    <div class="card">
        <h2>{{ strtoupper($type) }} Charts</h2>
        <p class="muted">ChartFox embedded charts for the selected aerodrome.</p>

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
        @if (! $chartUrl)
            <div class="empty-state">No ICAO found. Enter an ICAO code to load charts.</div>
        @else
            <div class="chart-embed">
                <iframe src="{{ $chartUrl }}" title="ChartFox {{ $icao }}" loading="lazy"></iframe>
            </div>
        @endif
    </div>
@endsection
