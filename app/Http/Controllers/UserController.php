<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use App\Http\Requests\User\GetUserDetailRequest;
use App\Http\Requests\User\GetListUsersRequest;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\User\Actions\GetUserDetailAction;
use App\Services\User\Actions\GetListUsersAction;
use App\Services\User\Actions\CreateUserAction;
use App\Services\User\Actions\UpdateUserAction;
use App\Services\User\Actions\DeleteUserAction;
use Throwable;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param GetListUsersRequest $request GetListUsersRequest
     *
     * @return mixed
     */
    public function index(GetListUsersRequest $request): mixed
    {
        $data = $request->validated();
        $result = (new GetListUsersAction())->handle($data);

        return response()->success($result);
    }

    /**
     *  Display the specified resource.
     *
     * @param int                  $id      ID
     * @param GetUserDetailRequest $request GetUserDetailRequest
     *
     * @return mixed
     */
    public function show(int $id, GetUserDetailRequest $request): mixed
    {
        $data = $request->validated();
        $result = (new GetUserDetailAction())->handle($id, $data);

        return response()->success($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateUserRequest $request CreateUserRequest
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function store(CreateUserRequest $request): mixed
    {
        $data = $request->validated();
        $user = (new CreateUserAction())->handle($data);

        return response()->success($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int               $id      ID
     * @param UpdateUserRequest $request CreateUserRequest
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function update(int $id, UpdateUserRequest $request, ): mixed
    {
        $data = $request->validated();
        $result = (new UpdateUserAction())->handle($id, $data);

        return response()->success($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id ID
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function destroy(int $id): mixed
    {
        (new DeleteUserAction())->handle($id);

        return response()->successWithoutData();
    }
}
