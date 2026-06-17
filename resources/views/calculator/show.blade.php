@extends('frontend.layout')

@section('content')
<section class="page-hero">
    <p class="eyebrow">Business Tool</p>
    <h1>Estimate long-term savings</h1>
    <p>Change the numbers and see an instant formula-based estimate for planning and comparison.</p>
</section>
<section class="calculator"
    data-rate="{{ $setting->electricity_rate }}"
    data-sun-hours="{{ $setting->sun_hours }}"
    data-co2-factor="{{ $setting->co2_factor }}">
    <div class="calc-form">
        <label>Monthly bill <input id="monthlyBill" type="number" value="5000" min="0"></label>
        <label>City
            <select id="city">
                @foreach($cities as $city)<option value="{{ $city->multiplier }}">{{ $city->name }}</option>@endforeach
            </select>
        </label>
        <label>Roof size sq. ft. <input id="roofSize" type="number" value="600" min="0"></label>
        <label>Usage
            <select id="usage">
                <option value="1">Home</option>
                <option value="1.15">Business</option>
                <option value="1.25">Industrial</option>
            </select>
        </label>
    </div>
    <div class="calc-results">
        <div><span>System size</span><strong id="systemSize">0 kW</strong></div>
        <div><span>Monthly savings</span><strong id="monthlySavings">₹0</strong></div>
        <div><span>Yearly savings</span><strong id="yearlySavings">₹0</strong></div>
        <div><span>25-year savings</span><strong id="longSavings">₹0</strong></div>
        <div><span>CO2 reduction</span><strong id="co2Reduction">0 tons</strong></div>
    </div>
</section>
@endsection
