<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $fillable = ['category_id','name','slug','image','meta_title','meta_description','order','is_active'];
    protected function casts(): array { return ['is_active'=>'boolean']; }
    public function category() { return $this->belongsTo(Category::class); }
    public function posts() { return $this->hasMany(Post::class); }
}
