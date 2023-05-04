<?php

namespace App\Services\Auth\Actions;

use App\Services\User\Tasks\GetUserDetailTask;
use App\Services\Action;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthAction extends Action
{
    /**
     * Execute action.
     *
     * @param array $credentials Credentials
     *
     * @return mixed
     */
    public function handle(array $credentials = []): mixed
    {
        try {
            DB::beginTransaction();

            // Using JWT Authentication
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->error([
                    'error'       => 'Unauthorized',
                    'message'     => 'Login failed!',
                    'status_code' => Response::HTTP_UNAUTHORIZED
                ]);
            }

            $user = (new GetUserDetailTask())->handle(null, $credentials);

            if (!$user) {
                return response()->error([
                    'error'       => 'user_not_found',
                    'message'     => 'User not found!',
                    'status_code' => Response::HTTP_BAD_REQUEST
                ]);
            }

            $user->jwtTokens()->create([
                'user_id' => $user->id,
                'token'   => $token,
            ]);

//            $user = (new GetUserDetailTask())->handle(null, $credentials);
//
//            // Using Sanctum Authentication
//            if (!Auth::attempt($credentials)) {
//                return response()->error([
//                    'error'       => 'user_not_found',
//                    'message'     => 'User not found!',
//                    'status_code' => Response::HTTP_BAD_REQUEST
//                ]);
//            }
//
//            $user = (new GetUserDetailTask())->handle(null, $credentials);
//
//            if (!Hash::check($credentials['password'], $user->password, [])) {
//                return $this->exception('Unauthorized', '', [], Response::HTTP_UNAUTHORIZED);
//            }
//
//            $token = $user->createToken('authToken')->plainTextToken;

            DB::commit();

            return response()->success([
                'token'      => $token,
                'user'       => $user,
                'token_type' => 'Bearer'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->error([
                'error'       => 'Error Server',
                'message'     => $e->getMessage(),
                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }
}
