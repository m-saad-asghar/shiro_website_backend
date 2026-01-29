<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiCacheHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only cache GET API requests
        if (
            $request->is('api/*') &&
            $request->isMethod('GET')
        ) {
            $response->headers->set(
                'Cache-Control',
                'public, max-age=3600, s-maxage=3600'
            );
        }

        return $response;
    }
}