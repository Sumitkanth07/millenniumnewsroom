@extends('admin.layout')
@section('content')
<h2>{{ $category->exists ? 'Edit Category' : 'Create Category' }}</h2>
<form class="panel form" method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" enctype="multipart/form-data">
    @csrf @if($category->exists) @method('PUT') @endif
    <label>Name <input name="name" value="{{ old('name', $category->name) }}" required></label>
    <label>Parent category
        <select name="parent_id">
            <option value="">Top level category</option>
            @foreach($parents as $parent)
                <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id) == $parent->id)>{{ $parent->name }}</option>
            @endforeach
        </select>
    </label>
    <label>Slug <input name="slug" value="{{ old('slug', $category->slug) }}"></label>
    <label>Category image <input name="image" type="file" accept="image/*"></label>
    <label>Description <textarea name="description" rows="3">{{ old('description', $category->description) }}</textarea></label>
    <label>Sort order <input name="sort_order" type="number" value="{{ old('sort_order', $category->sort_order ?? 0) }}"></label>
    <label>SEO title <input name="meta_title" value="{{ old('meta_title', $category->meta_title) }}"></label>
    <label>SEO description <textarea name="meta_description" rows="3">{{ old('meta_description', $category->meta_description) }}</textarea></label>
    <label class="check"><input name="is_active" type="checkbox" value="1" @checked(old('is_active', $category->is_active ?? true))> Active</label>
    <button class="btn primary">Save Category</button>
</form>
@endsection
