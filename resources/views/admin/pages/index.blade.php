@extends('admin.layout')

@section('content')
<div class="admin-title">
    <div>
        <span class="kicker">Content CMS</span>
        <h2>Pages</h2>
    </div>
    <a class="btn primary" href="{{ route('admin.pages.create') }}">Add Page</a>
</div>

<div class="panel">
@forelse($pages as $page)
    <div class="row-line">
        <div class="page-row-summary">
            @if($page->banner_image)
                <img src="{{ asset($page->banner_image) }}" alt="{{ $page->title }}">
            @endif
            <div>
                <strong>{{ $page->title }}</strong>
                <span>/{{ $page->slug }} - {{ $page->is_published ? 'Published' : 'Unpublished' }}</span>
                @if($page->meta_title)
                    <small>{{ $page->meta_title }}</small>
                @endif
            </div>
        </div>
        <div class="actions">
            <a class="btn small" href="{{ route('page.show', $page) }}" target="_blank" rel="noopener">View</a>
            <a class="btn small" href="{{ route('admin.pages.edit', $page) }}">Edit</a>
            <form method="POST" action="{{ route('admin.pages.toggle-publish', $page) }}">
                @csrf
                @method('PATCH')
                <button class="btn small">{{ $page->is_published ? 'Unpublish' : 'Publish' }}</button>
            </form>
            <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" onsubmit="return confirm('Delete this page?')">
                @csrf
                @method('DELETE')
                <button class="btn small danger">Delete</button>
            </form>
        </div>
    </div>
@empty
    <div class="empty-state">
        <strong>No pages found.</strong>
        <span>Create About Us, Privacy Policy, Terms, Contact or any custom page.</span>
    </div>
@endforelse
</div>

{{ $pages->links() }}
@endsection
