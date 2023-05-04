<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class BasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request Request
     * @param Closure $next    Next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->getUser() != config('auth.basic.username')
            || $request->getPassword() != config('auth.basic.password')) {
            throw new UnauthorizedHttpException('Basic');
        }

        return $next($request);
    }
}
