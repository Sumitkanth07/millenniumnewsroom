<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MediaLibraryController extends Controller
{
    public function index()
    {
        return view('admin.media.index', ['items' => MediaItem::latest()->paginate(24)]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'image', 'max:4096'],
            'folder' => ['nullable', 'string', 'max:80'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('file');
        $mimeType = $file->getMimeType();
        $size = $file->getSize();
        $folder = trim($request->input('folder', 'news'), '/') ?: 'news';
        $directory = public_path('uploads/media/'.$folder);

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $filename = time().'_'.uniqid().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();
        $file->move($directory, $filename);
        $path = 'uploads/media/'.$folder.'/'.$filename;

        MediaItem::create([
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'path' => $path,
            'mime_type' => $mimeType,
            'size' => $size,
            'folder' => $folder,
            'alt_text' => $request->input('alt_text'),
            'user_id' => $request->user()->id,
        ]);

        return back()->with('status', 'Media uploaded.');
    }

    public function destroy(MediaItem $medium)
    {
        $medium->delete();

        return back()->with('status', 'Media deleted.');
    }
}
