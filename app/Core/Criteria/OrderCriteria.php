<?php

namespace App\Core\Criteria;

use App\Core\Contracts\RepositoryInterface;
use App\Core\Contracts\CriteriaInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderCriteria.
 *
 * @package App\Core\Criteria;
 */
class OrderCriteria implements CriteriaInterface
{
    /**
     * @var array $order
     */
    protected array $orders;

    /**
     * Instance of OrderCriteria
     *
     * @param mixed $input
     */
    public function __construct(mixed $input)
    {
        $this->orders = array_filter(is_array($input) ? $input : explode(',', $input));

        foreach ($this->orders as &$order) {
            $order = trim($order);
        }
    }

    /**
     * Apply criteria in query repository
     *
     * @param Builder|Model       $model      Model
     * @param RepositoryInterface $repository Repository Interface
     *
     * @return mixed
     */
    public function apply(Model|Builder $model, RepositoryInterface $repository): mixed
    {
        foreach ($this->orders as $order) {
            $desc = str_starts_with($order, '-');
            $field = $desc ? substr($order, 1) : $order;

            $model = $desc ? $model->orderByDesc($field) : $model->orderBy($field);
        }
        return $model;
    }
}
