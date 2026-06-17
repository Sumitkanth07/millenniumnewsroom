@extends('admin.layout')
@section('content')
<div class="admin-title"><h2>Category Management</h2><a class="btn primary" href="{{ route('admin.categories.create') }}">New Category</a></div>
<div class="panel">
@foreach($categories as $category)
    <div class="row-line"><div><strong>{{ $category->name }}</strong><span>/category/{{ $category->slug }} · Order {{ $category->sort_order }}</span></div><div class="actions"><a class="btn small" href="{{ route('admin.categories.edit', $category) }}">Edit</a><form method="POST" action="{{ route('admin.categories.destroy', $category) }}">@csrf @method('DELETE')<button class="btn small danger">Delete</button></form></div></div>
@endforeach
</div>
{{ $categories->links() }}
@endsection
