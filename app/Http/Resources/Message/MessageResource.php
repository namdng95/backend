<?php

namespace App\Http\Resources\Message;

use App\Core\Resources\BaseResource;
use Illuminate\Http\Request;

class MessageResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request Request
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray($request = null): array
    {
        return $this->result([
            'id',
            'user_id',
            'message',
            'created_at',
            'updated_at',
            'deleted_at'
        ]);
    }
}
