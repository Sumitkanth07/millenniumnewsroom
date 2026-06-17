@extends('admin.layout')

@section('content')
<div class="admin-title"><h2>Redirects</h2><a class="btn primary" href="{{ route('admin.redirects.create') }}">New Redirect</a></div>
<div class="panel">
@foreach($redirects as $redirect)
    <div class="row-line">
        <div><strong>{{ $redirect->source }}</strong><span>{{ $redirect->status_code }} → {{ $redirect->destination }}</span></div>
        <div class="actions">
            <a class="btn small" href="{{ route('admin.redirects.edit', $redirect) }}">Edit</a>
            <form method="POST" action="{{ route('admin.redirects.destroy', $redirect) }}">@csrf @method('DELETE')<button class="btn small danger">Delete</button></form>
        </div>
    </div>
@endforeach
</div>
@endsection
