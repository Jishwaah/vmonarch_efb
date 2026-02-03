@extends('layouts.app')

@section('title', ($type) . ' Briefing | vMonarch EFB')
@section('header', ($type) . ' Briefing')

@section('content')
    <div class="card">
        <h2>{{ strtoupper($type) }} Aerodrome NOTAMs</h2>
        <p class="muted">NOTAMs pulled from the latest SimBrief OFP (aerodrome only).</p>

        <form class="weather-form" method="GET">
            <label>
                ICAO
                <input type="text" name="icao" value="{{ $icao }}" placeholder="EGLL" maxlength="4" />
            </label>
            <button class="btn btn-secondary" type="submit">Load NOTAMs</button>
        </form>
    </div>

    <div class="card">
        @if (! $icao)
            <div class="empty-state">No ICAO found. Enter an ICAO code to load NOTAMs.</div>
        @elseif (empty($notams))
            <div class="empty-state">No aerodrome NOTAMs found for {{ $icao }}.</div>
        @else
            <div class="message-list">
                @foreach ($notams as $notam)
                    <div class="message-item">
                        <div class="message-body">{{ $notam }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
