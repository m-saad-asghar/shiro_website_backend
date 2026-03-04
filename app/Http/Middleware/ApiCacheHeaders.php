<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiCacheHeaders
{
    public function handle(Request $request, Closure $next)
    {
        // ✅ Never cache admin APIs (roles, users, delete, etc.)
        // Adjust pattern if your routes are not under /api/admin/*
        if ($request->is('api/admin/*') || $request->is('admin/*')) {
            return $next($request)
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        }

        $response = $next($request);

        // ✅ Cache only public APIs (if you really want)
        return $response->header('Cache-Control', 'max-age=3600, public, s-maxage=3600');
    }
}
