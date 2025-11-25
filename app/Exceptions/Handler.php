<?php

namespace App\Exceptions;

use Throwable;
use App\Http\Traits\GeneralTrait;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    use GeneralTrait;

    protected $levels = [];

    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {
            return $this->handleException($e);
        }

        return parent::render($request, $e);
    }
    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return $this->unAuthorizeResponse();
        }

        return redirect()->guest(route('login'));
    }
}
