<?php

namespace App\Http\Controllers;

use App\Services\SimbriefService;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WeatherController extends Controller
{
    public function index(Request $request, SimbriefService $simbriefService, WeatherService $weatherService): View
    {
        $simbrief = $simbriefService->fetchLatestForUser($request->user());
        $flight = $simbrief['data'] ?? [];

        $departure = strtoupper((string) ($request->query('dep') ?? $flight['origin_icao'] ?? ''));
        $arrival = strtoupper((string) ($request->query('arr') ?? $flight['destination_icao'] ?? ''));

        $departureWeather = $departure ? $weatherService->fetchForAirport($departure) : null;
        $arrivalWeather = $arrival ? $weatherService->fetchForAirport($arrival) : null;

        return view('weather', [
            'simbrief' => $simbrief,
            'flight' => $flight,
            'departure' => $departure,
            'arrival' => $arrival,
            'departureWeather' => $departureWeather,
            'arrivalWeather' => $arrivalWeather,
        ]);
    }
}
