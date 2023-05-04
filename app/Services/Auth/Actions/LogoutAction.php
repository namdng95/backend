<?php

namespace App\Services\Auth\Actions;

use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Services\Action;

class LogoutAction extends Action
{
    /**
     * Execute action.
     *
     * @return void
     */
    public function handle(): void
    {
        if (!$user = authUser()) {
            return;
        }

        $token = request()->bearerToken();
        $user->jwtTokens()->where('token', $token)->delete();

        // JWT logout invalidate forever
        JWTAuth::parseToken()->invalidate(true);

        // sanctum logout
//        $user->tokens()->delete();

        Auth::logout();
    }
}
