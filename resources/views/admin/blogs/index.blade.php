@extends('admin.layout')

@section('content')
<div class="admin-title"><h2>Post Management</h2><a class="btn primary" href="{{ route('admin.blogs.create') }}">New Post</a></div>
<div class="panel">
@forelse($blogs as $blog)
    <div class="row-line">
        <div>
            <strong>{{ $blog->title }}</strong>
            <span>{{ $blog->category?->name ?? 'Uncategorized' }} · {{ $blog->author?->name ?? 'Staff desk' }} · {{ ucfirst($blog->status ?? ($blog->is_published ? 'published' : 'draft')) }}</span>
            <span>{{ parse_url($blog->publicUrl(), PHP_URL_PATH) }}</span>
        </div>
        <div class="actions">
            @if($blog->is_featured)<span class="badge">Featured</span>@endif
            @if($blog->is_breaking)<span class="badge gold">Breaking</span>@endif
            <a class="btn small" href="{{ route('admin.blogs.edit', $blog) }}">Edit</a>
            <form method="POST" action="{{ route('admin.blogs.destroy', $blog) }}">@csrf @method('DELETE')<button class="btn small danger">Delete</button></form>
        </div>
    </div>
@empty
    <p>No posts yet. Create the first newsroom story.</p>
@endforelse
</div>
{{ $blogs->links() }}
@endsection
