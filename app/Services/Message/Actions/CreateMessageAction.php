<?php

namespace App\Services\Message\Actions;

use Illuminate\Contracts\Container\BindingResolutionException;
use App\Services\Message\Tasks\CreateMessageTask;
use App\Services\Action;
use Throwable;

class CreateMessageAction extends Action
{
    /**
     * Execute action
     *
     * @param array $data Data
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function handle(array $data = []): mixed
    {
        $dataMessage = [
            'user_id'     => authId() ?? null,
            'message'    => $data['message'] ?? ''
        ];

        return (new CreateMessageTask())->handle($dataMessage);
    }
}
