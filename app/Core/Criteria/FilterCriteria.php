<?php

namespace App\Core\Criteria;

use App\Core\Contracts\RepositoryInterface;
use App\Core\Contracts\CriteriaInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class FilterCriteria.
 *
 * @package App\Core\Criteria;
 */
class FilterCriteria implements CriteriaInterface
{
    /**
     * @var array|Collection
     */
    protected Collection|array $input;

    /**
     * List of allowable fiters
     *
     * @var array|null
     */
    protected ?array $allows;

    /**
     * @var array
     */
    protected array $relationFilters = [];

    /**
     * Instance of FilterCriteria
     *
     * @param mixed      $input  Input
     * @param array|null $allows Allows
     */
    public function __construct(mixed $input, array $allows = null)
    {
        $this->input = [];

        if ($input instanceof Collection) {
            $this->input = $input->all();
        } elseif (is_array($input)) {
            $this->input = $input;
        }

        $this->allows = $allows;
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
        if (is_null($this->allows)
            && method_exists($repository, 'filterableFields')) {
            $this->allows = $repository->filterableFields();
        }

        if (is_null($this->allows)) {
            return $model;
        }

        foreach ($this->allows as $key => $value) {
            $filterName = is_string($key) ? $key : $value;
            $filter = is_string($key) ? $value : $this->getFilter($value);

            if (! isset($filterName) || ! isset($this->input[$filterName])) {
                continue;
            }

            if ($this->isValidFilter($filter)) {
                $model = $filter::apply($model, $this->input[$filterName], $repository);
                $this->prepareRelationFilters($filter, $this->input[$filterName]);

                continue;
            }

            $model = $model->where($filterName, $this->input[$filterName]);
        }

        return $this->applyRelationFilterQuery($model);
    }

    /**
     * Get Filter
     *
     * @param string $filterName Filter Name
     *
     * @return string
     */
    private function getFilter(string $filterName): string
    {
        return 'App\\Filters\\' . Str::studly($filterName);
    }

    /**
     * Checks if the class has been defined
     *
     * @param string $filter Filter
     *
     * @return bool
     */
    private function isValidFilter(string $filter): bool
    {
        return class_exists($filter);
    }

    /**
     * Prepare Relation Filters
     *
     * @param mixed $classFilterName Class Filter Name
     * @param mixed $input           Input
     *
     * @return void
     */
    private function prepareRelationFilters(mixed $classFilterName, mixed $input): void
    {
        $filters = preg_grep('/^(has|doesntHave)/', get_class_methods($classFilterName));

        foreach ($filters as $filter) {
            if (! array_key_exists($filter, $this->relationFilters)) {
                $this->relationFilters[$filter] = [];
            }

            $this->relationFilters[$filter][$classFilterName . '::' . $filter] = $input;
        }
    }

    /**
     * Apply Relation Filter Query
     *
     * @param Model $model Model
     *
     * @return mixed
     */
    private function applyRelationFilterQuery(Model $model): mixed
    {
        foreach ($this->relationFilters as $key => $filters) {
            $clause = Str::startsWith($key, 'has') ? 'whereHas' : 'whereDoesntHave';
            $relation = Str::startsWith($key, 'has') ? Str::substr($key, 3) : Str::substr($key, 10);

            $model = $model->$clause(Str::camel($relation), function ($query) use ($filters) {
                foreach ($filters as $filter => $input) {
                    $query = $filter($query, $input);
                }
            });
        }

        return $model;
    }
}
