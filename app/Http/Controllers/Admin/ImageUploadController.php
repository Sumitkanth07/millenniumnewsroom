<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ImageUploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'image', 'max:1024']
        ]);

        $file = $request->file('file');
        $baseName = time().'_'.uniqid().'_'.\Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $destination = public_path('uploads/editor');

        if (! is_dir($destination)) {
            mkdir($destination, 0775, true);
        }

        $optimized = \App\Helpers\ImageOptimizer::optimize($file, $destination, $baseName);
        $path = 'uploads/editor/' . $optimized['main'];

        $mimeType = 'image/webp';
        $size = file_exists(public_path($path)) ? filesize(public_path($path)) : $file->getSize();

        MediaItem::create([
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'path' => $path,
            'folder' => 'editor',
            'mime_type' => $mimeType,
            'size' => $size,
            'alt_text' => $request->input('alt_text'),
            'user_id' => $request->user()?->id,
        ]);

        return response()->json([
            'location' => asset($path),
            'path' => $path,
            'message' => 'Image uploaded successfully.',
        ]);
    }
}
