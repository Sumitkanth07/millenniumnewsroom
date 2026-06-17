@extends('admin.layout')

@section('content')
<h2>{{ $item->exists ? 'Edit Navigation Item' : 'Create Navigation Item' }}</h2>
<form class="panel form" method="POST" action="{{ $item->exists ? route('admin.navigation.update', $item) : route('admin.navigation.store') }}">
    @csrf @if($item->exists) @method('PUT') @endif
    <label>Label <input name="label" value="{{ old('label', $item->label) }}" required></label>
    <label>URL <input name="url" value="{{ old('url', $item->url) }}" required></label>
    <label>Sort order <input name="sort_order" type="number" value="{{ old('sort_order', $item->sort_order ?? 0) }}"></label>
    <label class="check"><input name="is_active" type="checkbox" value="1" @checked(old('is_active', $item->is_active ?? true))> Active</label>
    <button class="btn primary">Save</button>
</form>
@endsection
