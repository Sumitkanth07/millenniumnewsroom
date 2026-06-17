<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialPostLog extends Model
{
    protected $fillable = ['social_account_id','post_id','blog_id','platform','status','response','posted_at'];
    protected function casts(): array { return ['posted_at'=>'datetime']; }
    public function socialAccount() { return $this->belongsTo(SocialAccount::class); }
    public function post() { return $this->belongsTo(Post::class); }
    public function blog() { return $this->belongsTo(Blog::class); }
}
