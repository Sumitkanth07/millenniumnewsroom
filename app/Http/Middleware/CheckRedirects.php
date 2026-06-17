<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CheckRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin*') || app()->environment('testing')) {
            return $next($request);
        }

        try {
            if (Schema::hasTable('redirects')) {
                $source = '/'.trim($request->path(), '/');
                $redirect = Redirect::where('source', $source)->where('is_active', true)->first();

                if ($redirect) {
                    return redirect($redirect->destination, $redirect->status_code);
                }
            }
        } catch (Throwable) {
            return $next($request);
        }

        return $next($request);
    }
}
