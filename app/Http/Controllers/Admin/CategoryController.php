<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', ['categories' => Category::orderBy('sort_order')->paginate(20)]);
    }

    public function create()
    {
        return view('admin.categories.form', [
            'category' => new Category(),
            'parents' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        $data['is_active'] = $request->boolean('is_active', true);
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $baseName = time() . '_' . uniqid() . '_' . preg_replace('/[^A-Za-z0-9\-_]/', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $destination = public_path('uploads/categories');
            $optimized = \App\Helpers\ImageOptimizer::optimize($file, $destination, $baseName);
            $data['image'] = 'uploads/categories/' . $optimized['main'];
        } else {
            unset($data['image']);
        }
        Category::create($data);

        return redirect()->route('admin.categories.index')->with('status', 'Category saved.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.form', ['category' => $category, 'parents' => Category::whereKeyNot($category->id)->orderBy('name')->get()]);
    }

    public function update(Request $request, Category $category)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        $data['is_active'] = $request->boolean('is_active');
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $baseName = time() . '_' . uniqid() . '_' . preg_replace('/[^A-Za-z0-9\-_]/', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $destination = public_path('uploads/categories');
            $optimized = \App\Helpers\ImageOptimizer::optimize($file, $destination, $baseName);
            $data['image'] = 'uploads/categories/' . $optimized['main'];
        } else {
            unset($data['image']);
        }
        $category->update($data);

        return redirect()->route('admin.categories.index')->with('status', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return back()->with('status', 'Category deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'slug' => ['nullable', 'string', 'max:140'],
            'image' => ['nullable', 'image', 'max:1024'],
            'description' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
