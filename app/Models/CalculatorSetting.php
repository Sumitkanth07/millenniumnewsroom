<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalculatorSetting extends Model
{
    protected $fillable = ['electricity_rate', 'sun_hours', 'co2_factor'];
}
