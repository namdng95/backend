<?php

namespace App\Http\Middleware;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Enums\User\UserStatus;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Closure;

class ForceLogoutMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request Request
     * @param Closure $next    Closure
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $user = authUser();
//        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->error(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        if ($user->status == UserStatus::DISABLE) {
            $token = request()->bearerToken();
            JWTAuth::invalidate($token);

            $user->jwtTokens()->delete();
            auth('api')->logout();
        }

        return $next($request);
    }
}
