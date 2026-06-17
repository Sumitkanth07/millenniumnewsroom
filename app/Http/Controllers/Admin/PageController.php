<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        return view('admin.pages.index', ['pages' => Page::latest()->paginate(20)]);
    }

    public function create()
    {
        return view('admin.pages.form', ['page' => new Page()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['slug'] ?: $data['title']);
        $data['is_published'] = $request->boolean('is_published', true);
        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $this->storePublicUpload($request->file('banner_image'));
        }
        Page::create($data);

        return redirect()->route('admin.pages.index')->with('status', 'Page saved.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.form', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['slug'] ?: $data['title']);
        $data['is_published'] = $request->boolean('is_published');
        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $this->storePublicUpload($request->file('banner_image'));
        }
        $page->update($data);

        return redirect()->route('admin.pages.index')->with('status', 'Page updated.');
    }

    public function togglePublish(Page $page)
    {
        $page->update(['is_published' => ! $page->is_published]);

        return back()->with('status', $page->is_published ? 'Page published.' : 'Page unpublished.');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return back()->with('status', 'Page deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', 'unique:pages,slug'.($request->route('page') ? ','.$request->route('page')->id : '')],
            'banner_image' => ['nullable', 'image', 'max:4096'],
            'content' => ['required', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
        ]);
    }

    private function storePublicUpload($file): string
    {
        $directory = public_path('uploads/pages');
        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $filename = time().'_'.uniqid().'_'.preg_replace('/[^A-Za-z0-9.\-_]/', '_', $file->getClientOriginalName());
        $file->move($directory, $filename);

        return 'uploads/pages/'.$filename;
    }
}
