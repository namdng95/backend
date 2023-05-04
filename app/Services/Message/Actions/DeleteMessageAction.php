<?php

namespace App\Services\Message\Actions;

use Illuminate\Contracts\Container\BindingResolutionException;
use App\Services\Message\Tasks\DeleteMessageTask;
use App\Services\Action;
use Throwable;

class DeleteMessageAction extends Action
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
        return (new DeleteMessageTask())->handle($id);
    }
}
