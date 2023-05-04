<?php

namespace App\Http\Controllers;

use App\Http\Requests\Password\ForgotPasswordRequest;
use App\Http\Requests\Password\ResetPasswordRequest;
use App\Services\Password\Actions\ChangePasswordAction;
use App\Http\Requests\Password\ChangePasswordRequest;
use App\Services\Password\Actions\ForgotPasswordAction;
use App\Services\Password\Actions\ResetPasswordAction;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    /**
     * Change password
     *
     * @param ChangePasswordRequest $request Change Password Request
     *
     * @return mixed
     * @throws ValidationException
     */
    public function change(ChangePasswordRequest $request): mixed
    {
        $data = $request->validated();
        (new ChangePasswordAction())->handle($data);

        return response()->successWithoutData();
    }

    /**
     * Reset password
     *
     * @param string               $code    Code
     * @param ResetPasswordRequest $request request
     *
     * @return mixed
     * @throws ValidationException
     */
    public function reset(string $code, ResetPasswordRequest $request): mixed
    {
        $data = $request->validated();
        (new ResetPasswordAction())->handle($code, $data);

        return response()->successWithoutData();
    }

    /**
     * Forgot Password
     *
     * @param ForgotPasswordRequest $request Forgot Password Request
     *
     * @return mixed
     * @throws ValidationException
     */
    public function forgot(ForgotPasswordRequest $request): mixed
    {
        $data = $request->validated();
        $result = (new ForgotPasswordAction())->handle($data);

        return response()->success($result);
    }
}
