<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name', 'multiplier', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
