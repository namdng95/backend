<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Message\MessageResource;
use App\Core\Resources\BaseResource;
use Illuminate\Http\Request;

class UserResource extends BaseResource
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
        $result = $this->result([
            'id',
            'code',
            'name',
            'email',
            'logo',
            'status',
            'password_updated_at',
            'forgot_password_code',
            'forgot_password_time',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        if (empty($result['ips'] ?? '')) {
            $result['ips'] = [];
        }

        if (!empty($this->resource->messages)) {
            $result['messages'] = MessageResource::collection($this->whenLoaded('messages'));
        }

        return $result;
    }
}
