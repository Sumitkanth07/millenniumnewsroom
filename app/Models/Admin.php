<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $fillable = ['user_id', 'designation', 'is_active'];
    protected function casts(): array { return ['is_active' => 'boolean']; }
    public function user() { return $this->belongsTo(User::class); }
}
