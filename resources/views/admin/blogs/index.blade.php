@extends('admin.layout')

@section('content')
<div class="admin-title"><h2>Post Management</h2><a class="btn primary" href="{{ route('admin.blogs.create') }}">New Post</a></div>

<table class="admin-table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Category</th>
            <th>Author</th>
            <th>Status</th>
            <th>
                <a href="{{ route('admin.blogs.index', ['sort' => $currentSort === 'views_desc' ? 'views_asc' : 'views_desc']) }}" style="color: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                    Views
                    @if($currentSort === 'views_desc')
                        ↓
                    @elseif($currentSort === 'views_asc')
                        ↑
                    @else
                        ↕
                    @endif
                </a>
            </th>
            <th>Published</th>
            <th style="text-align: right;">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($blogs as $blog)
            <tr>
                <td>
                    <div style="display: flex; flex-direction: column;">
                        <strong style="font-size: 14px;">{{ $blog->title }}</strong>
                        <span style="font-size: 12px; opacity: 0.7; margin-top: 4px;">{{ parse_url($blog->publicUrl(), PHP_URL_PATH) }}</span>
                    </div>
                </td>
                <td>{{ $blog->category?->name ?? 'Uncategorized' }}</td>
                <td>{{ $blog->author?->name ?? 'Staff desk' }}</td>
                <td>
                    <div style="display: inline-flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                        <span class="badge {{ $blog->status === 'published' ? '' : ($blog->status === 'scheduled' ? 'gold' : 'gray') }}" style="text-transform: capitalize;">
                            {{ $blog->status ?? ($blog->is_published ? 'published' : 'draft') }}
                        </span>
                        @if($blog->is_featured)<span class="badge" style="font-size: 10px;">Featured</span>@endif
                        @if($blog->is_breaking)<span class="badge gold" style="font-size: 10px;">Breaking</span>@endif
                    </div>
                </td>
                <td style="font-weight: 600;">{{ number_format($blog->views_count) }}</td>
                <td>{{ $blog->published_at ? $blog->published_at->format('d M Y') : 'Not published' }}</td>
                <td>
                    <div style="display: flex; justify-content: flex-end; gap: 8px; align-items: center;">
                        <a class="btn small" href="{{ route('admin.blogs.edit', $blog) }}">Edit</a>
                        
                        <form method="POST" action="{{ route('admin.blogs.reset-views', $blog) }}" onsubmit="return confirm('Are you sure you want to reset views for this post?');" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn small" style="background-color: var(--secondaryColor, #c79a2b) !important; color: #fff !important;">Reset</button>
                        </form>

                        <form method="POST" action="{{ route('admin.blogs.destroy', $blog) }}" style="display: inline;">
                            @csrf @method('DELETE')
                            <button class="btn small danger" onsubmit="return confirm('Are you sure you want to delete this post?');">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 24px;">No posts yet. Create the first newsroom story.</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{ $blogs->links() }}
@endsection
