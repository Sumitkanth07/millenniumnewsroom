<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PostView;
use App\Models\Blog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TrackViews
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track successful GET requests
        if ($request->method() !== 'GET' || $response->getStatusCode() !== 200) {
            return $response;
        }

        // Do not track admin panel, ajax requests, or testing environments
        if ($request->is('admin*') || $request->ajax() || app()->environment('testing')) {
            return $response;
        }

        // Do not track if logged-in user is an admin
        if (auth()->check() && auth()->user()->is_admin) {
            return $response;
        }

        // Extract blog ID from path segments
        $segments = $request->segments();
        $blogId = null;
        if (count($segments) === 2 && !in_array($segments[0], ['page', 'author', 'admin', 'login', 'blog'], true)) {
            $blogSlug = $segments[1];
            $blogModel = Blog::where('slug', $blogSlug)->first();
            $blogId = $blogModel ? $blogModel->id : null;
        }

        $ipHash = md5($request->ip());
        $userAgent = $request->userAgent();
        $now = now();

        if ($blogId) {
            // Count a new view only if the same visitor opens the same post again after 30 seconds
            $recentView = PostView::where('blog_id', $blogId)
                ->where('ip_hash', $ipHash)
                ->where('user_agent', $userAgent)
                ->where('viewed_at', '>=', $now->copy()->subSeconds(30))
                ->exists();

            if (!$recentView) {
                PostView::create([
                    'blog_id' => $blogId,
                    'post_id' => null,
                    'ip_hash' => $ipHash,
                    'user_agent' => $userAgent,
                    'viewed_at' => $now,
                ]);

                // Increment cached views_count in blogs table
                DB::table('blogs')
                    ->where('id', $blogId)
                    ->increment('views_count');

                // Clear dashboard cache
                Cache::forget('admin.dashboard.payload');
            }
        } else {
            // General website view (non-post page: home, category, etc.)
            // Prevent rapid refresh spam within 30 seconds
            $recentView = PostView::whereNull('blog_id')
                ->where('ip_hash', $ipHash)
                ->where('user_agent', $userAgent)
                ->where('viewed_at', '>=', $now->copy()->subSeconds(30))
                ->exists();

            if (!$recentView) {
                PostView::create([
                    'blog_id' => null,
                    'post_id' => null,
                    'ip_hash' => $ipHash,
                    'user_agent' => $userAgent,
                    'viewed_at' => $now,
                ]);

                // Clear dashboard cache
                Cache::forget('admin.dashboard.payload');
            }
        }

        return $response;
    }
}
