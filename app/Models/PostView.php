<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostView extends Model
{
    protected $fillable = ['post_id','blog_id','ip_hash','user_agent','viewed_at'];
    protected function casts(): array { return ['viewed_at'=>'datetime']; }
    public function post() { return $this->belongsTo(Post::class); }
    public function blog() { return $this->belongsTo(Blog::class); }
}
