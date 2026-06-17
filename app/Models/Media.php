<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';
    protected $fillable = ['file_name','file_path','alt_text','caption','file_type','uploaded_by'];
    public function uploader() { return $this->belongsTo(User::class, 'uploaded_by'); }
    public function galleries() { return $this->hasMany(Gallery::class); }
}
