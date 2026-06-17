<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthorController extends Controller
{
    public function index()
    {
        return view('admin.authors.index', ['authors' => Author::withCount('blogs')->orderBy('name')->paginate(20)]);
    }

    public function create()
    {
        return view('admin.authors.form', ['author' => new Author()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        $data['is_active'] = $request->boolean('is_active', true);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('uploads/authors', 'public');
        } else {
            unset($data['image']);
        }
        Author::create($data);

        return redirect()->route('admin.authors.index')->with('status', 'Author saved.');
    }

    public function edit(Author $author)
    {
        return view('admin.authors.form', compact('author'));
    }

    public function update(Request $request, Author $author)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        $data['is_active'] = $request->boolean('is_active');
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('uploads/authors', 'public');
        } else {
            unset($data['image']);
        }
        $author->update($data);

        return redirect()->route('admin.authors.index')->with('status', 'Author updated.');
    }

    public function destroy(Author $author)
    {
        $author->delete();

        return back()->with('status', 'Author deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:140'],
            'email' => ['nullable', 'email', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
            'bio' => ['nullable', 'string'],
        ]);
    }
}
