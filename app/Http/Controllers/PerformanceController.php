<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PerformanceController extends Controller
{
    public function index(): View
    {
        return view('performance', [
            'performanceUrl' => config('services.simbrief.performance_url'),
        ]);
    }
}
