@extends('admin.layout')

@section('content')
<div class="admin-title">
    <div><span class="kicker">Homepage Builder</span><h2>Homepage Sections</h2></div>
    <a class="btn primary" href="{{ route('admin.homepage.create') }}">New Section</a>
</div>

<div class="panel">
@forelse($sections as $section)
    <div class="row-line">
        <div>
            <strong>{{ ucfirst(str_replace('_', ' ', $section->key)) }}</strong>
            <span>{{ $section->title }} / Order {{ $section->sort_order }} / {{ $section->is_active ? 'Active' : 'Hidden' }}</span>
        </div>
        <div class="actions">
            <a class="btn small" href="{{ route('admin.homepage.edit', $section) }}">Edit</a>
            <form method="POST" action="{{ route('admin.homepage.destroy', $section) }}">@csrf @method('DELETE')<button class="btn small danger">Delete</button></form>
        </div>
    </div>
@empty
    <div class="empty-state"><strong>No homepage sections found.</strong><span>Create a section to manage homepage visibility.</span></div>
@endforelse
</div>
@endsection
