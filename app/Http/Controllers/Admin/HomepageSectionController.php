<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomepageSectionController extends Controller
{
    public function index()
    {
        return view('admin.homepage.index', ['sections' => HomepageSection::orderBy('sort_order')->get()]);
    }

    public function create()
    {
        return view('admin.homepage.edit', ['section' => new HomepageSection()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image'] = $this->storePublicUpload($request->file('image'), 'homepage');
        }

        $data['is_active'] = $request->boolean('is_active');
        HomepageSection::create($data);
        Cache::forget('frontend.home.payload');

        return redirect()->route('admin.homepage.index')->with('status', 'Homepage section created.');
    }

    public function edit(HomepageSection $homepage)
    {
        return view('admin.homepage.edit', ['section' => $homepage]);
    }

    public function update(Request $request, HomepageSection $homepage)
    {
        $data = $this->validated($request, $homepage->id);

        $uploadedImage = $request->hasFile('image');

        if ($uploadedImage) {
            $data['image'] = $this->storePublicUpload($request->file('image'), 'homepage');
        }

        $data['is_active'] = $request->boolean('is_active');
        $homepage->update($data);
        Cache::forget('frontend.home.payload');

        $message = $uploadedImage
            ? 'Homepage section saved and image uploaded successfully.'
            : 'Homepage section saved successfully.';

        return redirect()->route('admin.homepage.index')->with('status', $message);
    }

    public function destroy(HomepageSection $homepage)
    {
        $homepage->delete();
        Cache::forget('frontend.home.payload');

        return redirect()->route('admin.homepage.index')->with('status', 'Homepage section deleted.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'key' => ['required', 'string', 'max:120', 'unique:homepage_sections,key'.($ignoreId ? ','.$ignoreId : '')],
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'button_text' => ['nullable', 'string', 'max:255'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);
    }

    private function storePublicUpload($file, string $folder): string
    {
        $directory = public_path('uploads/'.$folder);
        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $filename = time().'_'.uniqid().'_'.preg_replace('/[^A-Za-z0-9.\-_]/', '_', $file->getClientOriginalName());
        $file->move($directory, $filename);

        return 'uploads/'.$folder.'/'.$filename;
    }
}
