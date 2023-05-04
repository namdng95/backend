<?php

namespace App\Services\User\Actions;

use App\Http\Resources\User\UserCollection;
use App\Services\User\Tasks\GetListUsersTask;
use App\Services\Action;

class GetListUsersAction extends Action
{
    /**
     * Execute action
     *
     * @param array $data Data
     *
     * @return UserCollection
     */
    public function handle(array $data = []): UserCollection
    {
        $users = (new GetListUsersTask())->handle($data);

        return (new UserCollection($users));
    }
}
