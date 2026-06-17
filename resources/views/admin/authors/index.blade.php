@extends('admin.layout')
@section('content')
<div class="admin-title"><h2>Author Management</h2><a class="btn primary" href="{{ route('admin.authors.create') }}">New Author</a></div>
<div class="panel">
@foreach($authors as $author)
    <div class="row-line"><div><strong>{{ $author->name }}</strong><span>{{ $author->email }} · {{ $author->blogs_count }} posts</span></div><div class="actions"><a class="btn small" href="{{ route('admin.authors.edit', $author) }}">Edit</a><form method="POST" action="{{ route('admin.authors.destroy', $author) }}">@csrf @method('DELETE')<button class="btn small danger">Delete</button></form></div></div>
@endforeach
</div>
{{ $authors->links() }}
@endsection
