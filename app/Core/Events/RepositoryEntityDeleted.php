<?php

namespace App\Core\Events;

use Illuminate\Database\Eloquent\Model;

class RepositoryEntityDeleted
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * Construct.
     *
     * @param Model $model Model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}
