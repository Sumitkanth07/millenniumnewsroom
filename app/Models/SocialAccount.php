<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    protected $fillable = ['platform','account_name','api_token','platform_settings','auto_post','is_active'];
    protected function casts(): array { return ['platform_settings'=>'array','auto_post'=>'boolean','is_active'=>'boolean']; }
    public function logs() { return $this->hasMany(SocialPostLog::class); }
}
