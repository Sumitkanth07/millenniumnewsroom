<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = ['post_id','blog_id','media_id','image_path','alt_text','caption','sort_order'];
    public function post() { return $this->belongsTo(Post::class); }
    public function blog() { return $this->belongsTo(Blog::class); }
    public function media() { return $this->belongsTo(Media::class); }
}
