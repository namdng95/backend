<?php

namespace App\Services\Password\Actions;

use App\Services\User\Tasks\GetUserDetailTask;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\Action;

class ResetPasswordAction extends Action
{
    /**
     * Execute action.
     *
     * @param string $code Code
     * @param array  $data Data
     *
     * @return void
     * @throws ValidationException
     */
    public function handle(string $code, array $data = []): void
    {
        try {
            DB::beginTransaction();

            $data['forgot_password_code'] = $code;

            $user = (new GetUserDetailTask())->handle(null, $data);

            if (!$user) {
                $this->exception('password.reset.url_expired');
            }

            $user->update([
                'forgot_password_code' => null,
                'forgot_password_time' => null,
                'password_updated_at' => date('Y-m-d H:i:s')
            ]);

            $user->jwtTokens()->delete();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->exception('reset_password_failed');
        }
    }
}
