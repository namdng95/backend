<?php

namespace App\Services\User\Actions;

use App\Services\User\Tasks\GetUserDetailTask;
use App\Http\Resources\User\UserResource;
use App\Services\Action;

class GetUserDetailAction extends Action
{
    /**
     * Execute action
     *
     * @param int|null $id   ID
     * @param array    $data Data
     *
     * @return UserResource
     */
    public function handle(int $id = null, array $data = []): UserResource
    {
        $user = (new GetUserDetailTask())->handle($id, $data);

        return (new UserResource($user));
    }
}
