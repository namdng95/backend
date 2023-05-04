<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use App\Services\User\Actions\CreateUserAction;
use App\Services\Auth\Actions\LogoutAction;
use App\Services\Auth\Actions\AuthAction;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\Auth\AuthRequest;
use Throwable;

class AuthController extends Controller
{
    /**
     * Login
     *
     * @param AuthRequest $request Auth Request
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function login(AuthRequest $request): mixed
    {
        $data = $request->validated();
        $result = (new AuthAction())->handle($data);

        return response()->success($result);
    }

    /**
     * Register
     *
     * @param CreateUserRequest $request Create User Request
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function register(CreateUserRequest $request): mixed
    {
        $data = $request->validated();
        $user = (new CreateUserAction())->handle($data);

        return response()->success($user);
    }

    /**
     * Logout
     *
     * @return mixed
     */
    public function logout(): mixed
    {
        (new LogoutAction())->handle();

        return response()->successWithoutData();
    }
}
