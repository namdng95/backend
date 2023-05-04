<?php

namespace App\Core\Contracts;

interface RepositoryInterface
{
    /**
     * Get Model
     *
     * @return mixed
     */
    public function getModel(): mixed;
}
