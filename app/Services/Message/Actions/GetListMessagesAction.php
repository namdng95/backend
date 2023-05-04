<?php

namespace App\Services\Message\Actions;

use App\Http\Resources\Message\MessageCollection;
use App\Services\Message\Tasks\GetListMessagesTask;
use App\Services\Action;

class GetListMessagesAction extends Action
{
    /**
     * Execute action
     *
     * @param array $data Data
     *
     * @return MessageCollection
     */
    public function handle(array $data = []): MessageCollection
    {
        $messages = (new GetListMessagesTask())->handle($data);

        return (new MessageCollection($messages));
    }
}
