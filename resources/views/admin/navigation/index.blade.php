@extends('admin.layout')

@section('content')
<div class="admin-title"><h2>Navigation</h2><a class="btn primary" href="{{ route('admin.navigation.create') }}">New Item</a></div>
<div class="panel">
@foreach($items as $item)
    <div class="row-line">
        <div><strong>{{ $item->label }}</strong><span>{{ $item->url }}</span></div>
        <div class="actions">
            <a class="btn small" href="{{ route('admin.navigation.edit', $item) }}">Edit</a>
            <form method="POST" action="{{ route('admin.navigation.destroy', $item) }}">@csrf @method('DELETE')<button class="btn small danger">Delete</button></form>
        </div>
    </div>
@endforeach
</div>
@endsection
