<?php

namespace App\Services\Password\Actions;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\BusinessException;
use App\Services\Action;

class ChangePasswordAction extends Action
{
    /**
     * Execute action.
     *
     * @param array $data Data
     *
     * @return void
     * @throws ValidationException
     */
    public function handle(array $data = []): void
    {
        try {
            $user = auth()->user()->update(
//                ['password' => bcrypt($data['password'])],
                ['password' => Hash::make($data['password'])],
            );
        } catch (\Exception $e) {
            throw BusinessException::change_password_failed();
        }

        $bearerToken = request()->bearerToken();
        $user->jwtTokens()->where('token', '!=', $bearerToken)->delete();
    }
}
