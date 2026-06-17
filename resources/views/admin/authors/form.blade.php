@extends('admin.layout')
@section('content')
<h2>{{ $author->exists ? 'Edit Author' : 'Create Author' }}</h2>
<form class="panel form" method="POST" action="{{ $author->exists ? route('admin.authors.update', $author) : route('admin.authors.store') }}" enctype="multipart/form-data">
    @csrf @if($author->exists) @method('PUT') @endif
    <label>Name <input name="name" value="{{ old('name', $author->name) }}" required></label>
    <label>Slug <input name="slug" value="{{ old('slug', $author->slug) }}"></label>
    <label>Email <input name="email" type="email" value="{{ old('email', $author->email) }}"></label>
    <label>Author image <input name="image" type="file" accept="image/*"></label>
    <label>Bio <textarea name="bio" rows="6">{{ old('bio', $author->bio) }}</textarea></label>
    <label class="check"><input name="is_active" type="checkbox" value="1" @checked(old('is_active', $author->is_active ?? true))> Active</label>
    <button class="btn primary">Save Author</button>
</form>
@endsection
