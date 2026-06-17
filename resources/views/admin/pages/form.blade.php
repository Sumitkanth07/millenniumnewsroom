@extends('admin.layout')

@section('content')
<div class="admin-title">
    <div>
        <span class="kicker">Page CMS</span>
        <h2>{{ $page->exists ? 'Edit Page' : 'Add Page' }}</h2>
    </div>
    <a class="btn" href="{{ route('admin.pages.index') }}">Back to Pages</a>
</div>

<form class="panel form" method="POST" action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}" enctype="multipart/form-data">
    @csrf
    @if($page->exists) @method('PUT') @endif

    <label>Title
        <input name="title" value="{{ old('title', $page->title) }}" required>
    </label>

    <label>Slug
        <input name="slug" value="{{ old('slug', $page->slug) }}" placeholder="about-us">
        <small>Leave blank to generate from the title. The slug controls the frontend page URL.</small>
    </label>

    @if($page->banner_image)
        <div class="preview-box">
            <span>Current banner image</span>
            <img src="{{ asset($page->banner_image) }}" alt="{{ $page->title }}">
            <small>{{ $page->banner_image }}</small>
        </div>
    @endif

    <label>Featured / Banner image
        <input name="banner_image" type="file" accept="image/*">
    </label>

    <label>Content
        <textarea id="content" name="content" rows="14" required>{{ old('content', $page->content) }}</textarea>
    </label>

    <label>SEO title
        <input name="meta_title" value="{{ old('meta_title', $page->meta_title) }}" maxlength="255">
    </label>

    <label>SEO description
        <textarea name="meta_description" rows="3">{{ old('meta_description', $page->meta_description) }}</textarea>
    </label>

    <label class="check">
        <input name="is_published" type="checkbox" value="1" @checked(old('is_published', $page->exists ? $page->is_published : true))>
        Published
    </label>

    <button class="btn primary">Save Page</button>
</form>

@include('admin.partials.tinymce')
@endsection
