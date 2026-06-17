<?php

namespace App\Http\Controllers;

use App\Models\CalculatorSetting;
use App\Models\City;

class CalculatorController extends Controller
{
    public function show()
    {
        return view('calculator.show', [
            'setting' => CalculatorSetting::firstOrCreate([], ['electricity_rate' => 8, 'sun_hours' => 4.5, 'co2_factor' => 1.35]),
            'cities' => City::where('is_active', true)->orderBy('name')->get(),
            'metaTitle' => 'Business Tools | MILLENNIUM NEWSROOM',
            'metaDescription' => 'Use MILLENNIUM NEWSROOM tools and explainers for practical business and market decisions.',
        ]);
    }
}
