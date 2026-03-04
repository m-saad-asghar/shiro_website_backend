<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\NoCache;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->append(\App\Http\Middleware\ForceJsonResponse::class);
        $middleware->append(\App\Http\Middleware\Localization::class);

        // API-only middleware (equivalent to Kernel.php api group)
$middleware->appendToGroup('api', [
    \Illuminate\Http\Middleware\HandleCors::class,
\App\Http\Middleware\ApiCacheHeaders::class,
]);

 $middleware->alias([
        'no-cache' => NoCache::class,
    ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        return new \App\Exceptions\Handler(app());
    })
    ->withProviders([
        \App\Providers\ApiExceptionServiceProvider::class,
    ])
    ->create();
