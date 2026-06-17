@extends('admin.layout')

@section('content')
<h2>Calculator Settings</h2>
<form class="panel form" method="POST" action="{{ route('admin.calculator.update') }}">
    @csrf @method('PUT')
    <label>Electricity rate per unit <input name="electricity_rate" type="number" step="0.01" value="{{ $setting->electricity_rate }}"></label>
    <label>Average sun hours <input name="sun_hours" type="number" step="0.01" value="{{ $setting->sun_hours }}"></label>
    <label>CO2 factor <input name="co2_factor" type="number" step="0.01" value="{{ $setting->co2_factor }}"></label>
    <h3>City Multipliers</h3>
    @foreach($cities as $city)
        <div class="inline-fields">
            <input name="cities[{{ $city->id }}][name]" value="{{ $city->name }}">
            <input name="cities[{{ $city->id }}][multiplier]" type="number" step="0.01" value="{{ $city->multiplier }}">
            <label class="check"><input name="cities[{{ $city->id }}][is_active]" type="checkbox" @checked($city->is_active)> Active</label>
        </div>
    @endforeach
    <h3>Add City</h3>
    <div class="inline-fields">
        <input name="new_city_name" placeholder="City name">
        <input name="new_city_multiplier" type="number" step="0.01" placeholder="1.00">
    </div>
    <button class="btn primary">Save Settings</button>
</form>
@endsection
