<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalculatorSetting;
use App\Models\City;
use Illuminate\Http\Request;

class CalculatorSettingController extends Controller
{
    public function edit()
    {
        return view('admin.calculator.edit', [
            'setting' => CalculatorSetting::firstOrCreate([], ['electricity_rate' => 8, 'sun_hours' => 4.5, 'co2_factor' => 1.35]),
            'cities' => City::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'electricity_rate' => ['required', 'numeric', 'min:1'],
            'sun_hours' => ['required', 'numeric', 'min:1'],
            'co2_factor' => ['required', 'numeric', 'min:0'],
            'cities' => ['array'],
            'cities.*.name' => ['required', 'string', 'max:255'],
            'cities.*.multiplier' => ['required', 'numeric', 'min:0.1'],
            'cities.*.is_active' => ['nullable', 'boolean'],
        ]);

        CalculatorSetting::first()->update($request->only(['electricity_rate', 'sun_hours', 'co2_factor']));

        foreach ($request->input('cities', []) as $id => $city) {
            City::whereKey($id)->update([
                'name' => $city['name'],
                'multiplier' => $city['multiplier'],
                'is_active' => isset($city['is_active']),
            ]);
        }

        if ($request->filled('new_city_name')) {
            City::create([
                'name' => $request->input('new_city_name'),
                'multiplier' => $request->input('new_city_multiplier', 1),
                'is_active' => true,
            ]);
        }

        return back()->with('status', 'Calculator settings saved.');
    }
}
