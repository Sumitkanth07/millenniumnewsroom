<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Author extends Model
{
    protected $fillable = ['name', 'slug', 'email', 'image', 'bio', 'designation', 'social_links', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'social_links' => 'array'];
    }

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public static function slugFrom(string $name): string
    {
        return Str::slug($name) ?: 'author';
    }
}
