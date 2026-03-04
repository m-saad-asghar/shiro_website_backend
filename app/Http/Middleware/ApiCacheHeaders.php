<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ApiCacheHeaders
{
    public function handle(Request $request, Closure $next)
    {
        // Never touch preflight. Let HandleCors do its job.
        if ($request->isMethod('OPTIONS')) {
            return $next($request);
        }

        $response = $next($request);

        // Only set headers if it's a real Symfony response (it will be).
        if ($response instanceof SymfonyResponse) {

            // ✅ Never cache admin routes
            if ($request->is('api/admin/*') || $request->is('admin/*')) {
                $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
                $response->headers->set('Pragma', 'no-cache');
                $response->headers->set('Expires', '0');
                return $response;
            }

            // ✅ Cache public APIs (optional)
            $response->headers->set('Cache-Control', 'max-age=3600, public, s-maxage=3600');
        }

        return $response;
    }
}