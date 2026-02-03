<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AcarsController extends Controller
{
    public function index(): View
    {
        return view('acars');
    }
}
