<?php

namespace App\Services\User\Actions;

use Illuminate\Contracts\Container\BindingResolutionException;
use App\Services\User\Tasks\DeleteUserTask;
use App\Services\Action;
use Throwable;

class DeleteUserAction extends Action
{
    /**
     * Execute action
     *
     * @param int|null $id ID
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function handle(int $id = null): mixed
    {
        return (new DeleteUserTask())->handle($id);
    }
}
