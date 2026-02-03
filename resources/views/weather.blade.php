@extends('layouts.app')

@section('title', 'Weather | vMonarch EFB')
@section('header', 'Weather')

@section('content')
    <div class="card">
        <h2>METAR &amp; TAF</h2>
        <p class="muted">
            Weather data is pulled from AviationWeather.gov. If SimBrief does not supply airports, enter them manually.
        </p>
        <form class="form weather-form" method="GET" action="{{ route('weather') }}">
            <label>
                Departure ICAO
                <input type="text" name="dep" value="{{ $departure }}" placeholder="EGLL" maxlength="4" />
            </label>
            <label>
                Arrival ICAO
                <input type="text" name="arr" value="{{ $arrival }}" placeholder="KJFK" maxlength="4" />
            </label>
            <button class="btn btn-primary" type="submit">Fetch Weather</button>
        </form>
    </div>

    <div class="weather-grid">
        @include('partials.weather-card', [
            'title' => 'Departure',
            'airport' => $departure,
            'weather' => $departureWeather,
        ])

        @include('partials.weather-card', [
            'title' => 'Arrival',
            'airport' => $arrival,
            'weather' => $arrivalWeather,
        ])
    </div>
@endsection
