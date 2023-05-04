<?php

namespace App\Core\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface CriteriaInterface
{
    /**
     * Apply the criteria
     *
     * @param Builder|Model       $model      Model
     * @param RepositoryInterface $repository Repository
     *
     * @return mixed
     */
    public function apply(Model|Builder $model, RepositoryInterface $repository): mixed;
}
