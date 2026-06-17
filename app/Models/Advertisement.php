<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $fillable = ['name','placement','code','is_responsive','is_active'];
    protected function casts(): array { return ['is_responsive'=>'boolean','is_active'=>'boolean']; }
}
