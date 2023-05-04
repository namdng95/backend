<?php

namespace App\Http\Middleware;

use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Exception;
use Closure;

class AuthenticateJWT
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
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenInvalidException $e) {
            return response()->error([
                'error' => 'Invalid token',
                'message' => $e->getMessage()
                ], Response::HTTP_UNAUTHORIZED);
        } catch (TokenExpiredException $e) {
            return response()->error([
                'error' => 'Expired token',
                'message' => $e->getMessage()
                ], Response::HTTP_UNAUTHORIZED);
        } catch (JWTException|Exception $e) {
            return response()->error([
                'error' => 'Token not found',
                'message' => $e->getMessage()
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!$user) {
            return response()->error(['Unauthorized' => 'User not found!'], Response::HTTP_UNAUTHORIZED);
        }

//        $request->merge(['user' => $user]);

        return $next($request);
    }
}
