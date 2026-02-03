<?php

namespace App\Http\Controllers;

use App\Services\SimbriefService;
use Illuminate\View\View;

class OfpController extends Controller
{
    public function index(SimbriefService $simbriefService): View
    {
        $result = $simbriefService->fetchLatestForUser(auth()->user());

        return view('ofp', [
            'simbrief' => $result,
            'flight' => $result['data'] ?? null,
        ]);
    }
}
