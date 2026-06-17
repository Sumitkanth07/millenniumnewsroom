@extends('admin.layout')

@section('content')
<div class="admin-hero">
    <div><span class="kicker">MILLENNIUM NEWSROOM CMS</span><h2>Dashboard</h2><p>Manage publishing, SEO, media and homepage performance from one newsroom console.</p></div>
    <a class="btn primary" href="{{ route('admin.blogs.create') }}">Create Story</a>
</div>
<div class="admin-grid dashboard-cards">
    @foreach($stats as $label => $value)
        <div class="admin-card metric-card"><span>{{ $label }}</span><strong>{{ number_format($value) }}</strong><small>{{ $loop->iteration % 2 ? 'Updated live' : 'Publishing health' }}</small></div>
    @endforeach
</div>
<div class="analytics-grid">
    <section class="panel">
        <div class="section-head"><h3>Recent Posts</h3><a href="{{ route('admin.blogs.index') }}">Manage</a></div>
        @foreach($recentBlogs as $blog)
            <div class="row-line"><div><strong>{{ $blog->title }}</strong><span>{{ $blog->category?->name ?? 'Uncategorized' }} · {{ $blog->is_published ? 'Published' : 'Draft' }}</span></div><span class="badge">{{ number_format($blog->views_count) }} views</span></div>
        @endforeach
    </section>
    <section class="panel">
        <h3>Trending Analytics</h3>
        <div class="chart-grid">
            @foreach($trendingBlogs as $blog)
                <div class="bar-row"><span>{{ $blog->title }}</span><i style="width:{{ min(100, max(8, $blog->views_count / max(1, $trendingBlogs->max('views_count')) * 100)) }}%"></i></div>
            @endforeach
        </div>
    </section>
</div>
@endsection
