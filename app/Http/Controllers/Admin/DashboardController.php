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
                'Views' => Blog::sum('views_count'),
            ],
            'recentBlogs' => Blog::with('category')->latest()->take(5)->get(),
            'trendingBlogs' => Blog::with('category')->orderByDesc('views_count')->take(5)->get(),
        ]);

        return view('admin.dashboard.index', $payload);
    }
}
