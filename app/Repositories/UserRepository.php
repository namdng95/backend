<?php

namespace App\Repositories;

use App\Core\Repositories\BaseRepository;
use App\Models\User;

class UserRepository extends BaseRepository
{
    /**
     * Get Model
     *
     * @return string
     */
    public function getModel (): string
    {
        return User::class;
    }
}
