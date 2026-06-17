<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    protected $fillable = ['seoable_type','seoable_id','meta_title','meta_description','meta_keywords','canonical_url','robots_meta','og_title','og_description','og_image','schema_type','schema_data','include_in_sitemap'];
    protected function casts(): array { return ['schema_data'=>'array','include_in_sitemap'=>'boolean']; }
    public function seoable() { return $this->morphTo(); }
}
