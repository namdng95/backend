<?php

namespace App\Core\Criteria;

use App\Core\Contracts\RepositoryInterface;
use App\Core\Contracts\CriteriaInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WithRelationsCriteriaCriteria.
 *
 * @package App\Core\Criteria;
 */
class WithCountRelationsCriteria implements CriteriaInterface
{
    /**
     * List of request relations from query string
     *
     * @var array
     */
    protected array $input;

    /**
     * List of allow relations
     *
     * @var array|null
     */
    protected ?array $allows;

    /**
     * A constructor of WithRelationsCriteria
     *
     * @param mixed $input  Input
     * @param array $allows Allows
     */
    public function __construct(mixed $input = '', array $allows = [])
    {
        $this->input = array_filter(
            array_map(function ($input) {
                return trim('\Illuminate\Support\Str::camel', $input);
            },
                is_array($input) ? $input : explode(',', $input)
            )
        );

        $this->allows = $allows;
    }

    /**
     * Apply criteria in query repository
     *
     * @param Model|Builder       $model      Model
     * @param RepositoryInterface $repository Repository
     *
     * @return mixed
     */
    public function apply(Model|Builder $model, RepositoryInterface $repository): mixed
    {
        $tmpModel = $model;

        if ($model instanceof Builder) {
            $tmpModel = $model->getModel();
        }

        $withs = [];

        foreach ($this->input as $with) {
            if (in_array($with, $withs)) {
                continue;
            }

            if (method_exists($tmpModel, $with)) {
                $withs[] = $with;
            }
        }

        return empty($withs) ? $model : $model->withCount($withs);
    }
}
