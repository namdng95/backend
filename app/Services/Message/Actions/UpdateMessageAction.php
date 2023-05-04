<?php

namespace App\Services\Message\Actions;

use Illuminate\Contracts\Container\BindingResolutionException;
use App\Services\Action;
use Throwable;

class UpdateMessageAction extends Action
{
    /**
     * Execute action
     *
     * @param int|null $id   ID
     * @param array    $data Data
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function handle(int $id = null, array $data = []): mixed
    {
        $message = (new GetMessageDetailAction())->handle($id);

        $dataMessage = [
            'user_id'     => authId() ?? null,
            'message'    => $data['message'] ?? ''
        ];

        $message->update($dataMessage);

        return $message->refresh();
    }
}
