<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Blog;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $payload = Cache::remember('admin.dashboard.payload', 60, fn () => [
            'stats' => [
                'Total Posts' => Blog::count(),
                'Categories' => Category::count(),
                'Authors' => Author::count(),
                'Total Website Views' => \App\Models\PostView::count(),
                'Total Post Views' => Blog::sum('views_count'),
                'Today\'s Views' => \App\Models\PostView::where('viewed_at', '>=', now()->startOfDay())->count(),
                'Last 7 Days Views' => \App\Models\PostView::where('viewed_at', '>=', now()->subDays(7)->startOfDay())->count(),
                'Last 30 Days Views' => \App\Models\PostView::where('viewed_at', '>=', now()->subDays(30)->startOfDay())->count(),
            ],
            'recentBlogs' => Blog::with('category')->latest()->take(5)->get(),
            'trendingBlogs' => Blog::with('category')->orderByDesc('views_count')->take(5)->get(),
            'mostViewedBlogs' => Blog::with('category')->orderByDesc('views_count')->take(10)->get(),
        ]);

        return view('admin.dashboard.index', $payload);
    }
}
