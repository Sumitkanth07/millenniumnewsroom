@extends('admin.layout')
@section('content')
<h2>Media Library</h2>
<form class="panel form" method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="inline-fields">
        <label>Image <input name="file" type="file" accept="image/*" required></label>
        <label>Folder <input name="folder" value="{{ old('folder', 'news') }}"></label>
        <label>Alt text <input name="alt_text" value="{{ old('alt_text') }}"></label>
    </div>
    <button class="btn primary">Upload</button>
</form>
<div class="media-grid">
@forelse($items as $item)
    <article class="media-item">
        <img src="{{ asset($item->path) }}" alt="{{ $item->alt_text ?: $item->name }}" loading="lazy" decoding="async">
        <strong>{{ $item->name }}</strong>
        <small>{{ $item->folder }}</small>
        <form method="POST" action="{{ route('admin.media.destroy', $item) }}">@csrf @method('DELETE')<button class="btn small danger">Delete</button></form>
    </article>
@empty
    <article class="media-item empty-state"><strong>No media uploaded yet.</strong><small>Upload an image to start building the library.</small></article>
@endforelse
</div>
{{ $items->links() }}
@endsection
