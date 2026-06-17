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
            'file' => ['required', 'image', 'max:4096']
        ]);

        $file = $request->file('file');
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        $filename = time().'_'.uniqid().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();

        $destination = public_path('uploads/editor');

        if (! is_dir($destination)) {
            mkdir($destination, 0775, true);
        }

        $file->move($destination, $filename);

        $path = 'uploads/editor/' . $filename;

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
