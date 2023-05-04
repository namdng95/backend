<?php

namespace App\Repositories;

use App\Core\Repositories\BaseRepository;
use App\Models\Message;

class MessageRepository extends BaseRepository
{
    /**
     * Get Model
     *
     * @return string
     */
    public function getModel (): string
    {
        return Message::class;
    }
}
