@extends('admin.layout')

@section('content')
<div class="admin-title">
    <div><span class="kicker">Homepage Builder</span><h2>{{ $section->exists ? 'Edit' : 'Create' }} Section</h2></div>
</div>

<form class="panel form" method="POST" action="{{ $section->exists ? route('admin.homepage.update', $section) : route('admin.homepage.store') }}" enctype="multipart/form-data">
    @csrf
    @if($section->exists) @method('PUT') @endif

    <label>Key <input name="key" value="{{ old('key', $section->key) }}" placeholder="latest_news" required></label>
    <label>Title <input name="title" value="{{ old('title', $section->title) }}"></label>
    <label>Subtitle <input name="subtitle" value="{{ old('subtitle', $section->subtitle) }}"></label>
    <label>Content <textarea name="content" rows="6">{{ old('content', $section->content) }}</textarea></label>
    <label>Button text <input name="button_text" value="{{ old('button_text', $section->button_text) }}"></label>
    <label>Button URL <input name="button_url" value="{{ old('button_url', $section->button_url) }}"></label>
    <label>Sort order <input name="sort_order" type="number" value="{{ old('sort_order', $section->sort_order ?? 0) }}" required></label>

    @if($section->image)
        <div class="preview-box">
            <span>Current image</span>
            <img src="{{ asset($section->image) }}" alt="{{ $section->title }}">
            <small>Saved as {{ $section->image }}</small>
        </div>
    @endif

    <label>Image <input name="image" type="file" accept="image/*"></label>
    <label class="check"><input name="is_active" type="checkbox" value="1" @checked(old('is_active', $section->is_active ?? true))> Active</label>
    <button class="btn primary">Save</button>
</form>
@endsection
