<?php

namespace App\Core\Contracts;

use Illuminate\Database\Eloquent\Model;

interface FilterInterface
{
    /**
     * Apply the filter
     *
     * @param Model $model Model
     * @param mixed $input Input
     *
     * @return mixed
     */
    public static function apply(Model $model, mixed $input): mixed;
}
