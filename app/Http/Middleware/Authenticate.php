<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param Request $request Request
     *
     * @return string|null
     * @throws AuthenticationException
     */
    protected function redirectTo($request): ?string
    {
        if (! $request->expectsJson()) {
            if (isApi() || isAjax()) {
                throw new AuthenticationException();
            }

            return route('welcome');
        }
    }
}
