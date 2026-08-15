<?php

use App\Http\Middleware\AdminOnly;
use App\Http\Middleware\CheckRedirects;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'newsletter/unsubscribe/*',
        ]);

        $middleware->web(append: [
            CheckRedirects::class,
            SecurityHeaders::class,
            \App\Http\Middleware\TrackViews::class,
        ]);

        $middleware->alias([
            'admin' => AdminOnly::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception, $request) {
            if (!$request->is('admin*')) {
                $path = '/' . trim($request->path(), '/');
                $logs = \Illuminate\Support\Facades\Cache::get('seo_404_logs', []);
                if (isset($logs[$path])) {
                    $logs[$path]['hits']++;
                    $logs[$path]['last_hit'] = date('Y-m-d H:i:s');
                } else {
                    $logs[$path] = [
                        'path' => $path,
                        'hits' => 1,
                        'last_hit' => date('Y-m-d H:i:s')
                    ];
                }
                if (count($logs) > 100) {
                    $logs = array_slice($logs, -100, null, true);
                }
                \Illuminate\Support\Facades\Cache::put('seo_404_logs', $logs, 86400 * 7);
            }
            return null;
        });

        $exceptions->render(function (AuthenticationException $exception, $request) {
            if ($request->expectsJson()) {
                return null;
            }

            return redirect()->guest('/admin/login');
        });
    })->create();
