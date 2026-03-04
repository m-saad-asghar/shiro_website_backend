<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiCacheHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Admin routes: disable cache
        if ($request->is('api/admin/*') || $request->is('admin/*')) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            return $response;
        }

        // Public APIs: cache
        $response->headers->set('Cache-Control', 'public, max-age=3600, s-maxage=3600');

        return $response;
    }
}