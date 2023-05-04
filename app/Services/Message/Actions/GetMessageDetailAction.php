<?php

namespace App\Services\Message\Actions;

use App\Http\Resources\Message\MessageResource;
use App\Services\Message\Tasks\GetMessageDetailTask;
use App\Services\Action;

class GetMessageDetailAction extends Action
{
    /**
     * Execute action
     *
     * @param int|null $id   ID
     * @param array    $data Data
     *
     * @return MessageResource
     */
    public function handle(int $id = null, array $data = []): MessageResource
    {
        $message = (new GetMessageDetailTask())->handle($id, $data);

        return (new MessageResource($message));
    }
}
