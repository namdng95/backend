<?php

namespace App\Core\Repositories;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Core\Exceptions\RepositoryException;
use App\Core\Contracts\RepositoryInterface;
use App\Core\Traits\HasCriteria;
use App\Core\Traits\HasScope;
use Throwable;
use Closure;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class BaseRepository implements RepositoryInterface
{
    use HasScope, HasCriteria;

    /**
     * @var Application
     */
    protected Application $app;

    /**
     * @var Model|Builder|\Illuminate\Database\Query\Builder
     */
    protected $model;

    /**
     * Construct.
     *
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->resetModel();
        $this->resetScope();
        $this->resetCriteria();
    }

    /**
     * Get the model of repository
     *
     * @return string
     */
    abstract public function getModel(): string;

    /**
     * Get Table
     *
     * @param bool $isRaw Is Raw
     *
     * @return string
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public static function getTable(bool $isRaw = false): string
    {
        $tableName = (new static(app()))->model->getTable();

        if ($isRaw) {
            $tableName = config('database.prefix') . $tableName;
        }

        return $tableName;
    }

    /**
     * Reset Model
     *
     * @return Model
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function resetModel(): Model
    {
        $instance = $this->app->make($this->getModel());

        if (! $instance instanceof Model) {
            throw RepositoryException::invalidModel();
        }

        return $this->model = $instance;
    }

    /**
     * Left Join
     *
     * @param string $table   Table
     * @param mixed $callback Callback
     *
     * @return $this
     */
    public function leftJoin(string $table, mixed $callback): static
    {
        $this->model = $this->model->leftJoin($table, $callback);

        return $this;
    }

    /**
     * Join Table
     *
     * @param mixed      $table    Table
     * @param mixed      $first    First
     * @param mixed|null $operator Operator
     * @param mixed|null $second   Second
     * @param string     $type     Type
     * @param bool       $where    Where
     *
     * @return $this
     */
    public function join(
        mixed $table,
        mixed $first,
        mixed $operator = null,
        mixed $second = null,
        string $type = 'inner',
        bool $where = false
    ): static
    {
        if (empty($second) && !empty($operator)) {
            $second = $operator;
            $operator = '=';
        }

        $this->model = $this->model->join($table, $first, $operator, $second, $type, $where);

        return $this;
    }

    /**
     * Select Raw
     *
     * @param mixed $raw Raw
     *
     * @return $this
     */
    public function selectRaw(mixed $raw): static
    {
        $this->model = $this->model->selectRaw($raw);

        return $this;
    }

    /**
     * Select
     *
     * @param mixed $raw Raw
     *
     * @return $this
     */
    public function select(mixed $raw): static
    {
        $this->model = $this->model->select($raw);

        return $this;
    }

    /**
     * Group By
     *
     * @param mixed $field Field
     *
     * @return $this
     */
    public function groupBy(mixed $field): static
    {
        $this->model = $this->model->groupBy($field);

        return $this;
    }

    /**
     * To Sql
     *
     * @return string
     */
    public function toSql(): string
    {
        return $this->model->toSql();
    }

    /**
     * Load relations
     *
     * @param array $relations Relations
     *
     * @return $this
     */
    public function with(array $relations): static
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    /**
     * With Trashed
     *
     * @return $this
     */
    public function withTrashed(): static
    {
        $this->model = $this->model->withTrashed();

        return $this;
    }

    /**
     * Prepare for querying
     *
     * @return void
     */
    private function prepareQuery(): void
    {
        $this->applyCriteria();
        $this->applyScope();
    }

    /**
     * Rescue the query after performed
     *
     * @return void
     * @throws BindingResolutionException
     * @throws Throwable
     */
    private function rescueQuery(): void
    {
        $this->resetModel();
        $this->resetScope();
        $this->resetCriteria();
    }

    /**
     * Find record by id
     *
     * @param int   $id      ID
     * @param array $columns Columns
     *
     * @return Model
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function find(int $id, array $columns = ['*']): Model
    {
        $this->prepareQuery();
        $result = $this->model->find($id, $columns);
        $this->rescueQuery();

        return $result;
    }

    /**
     * Chunk By Id
     *
     * @param int     $limit    Limit
     * @param Closure $callback Callback
     *
     * @return bool
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function chunkById(int $limit, Closure $callback): bool
    {
        $this->prepareQuery();
        $result = $this->model->chunkById($limit, $callback);
        $this->rescueQuery();

        return $result;
    }

    /**
     * First Or Create
     *
     * @param array $attributes Attributes
     * @param array $values     Values
     *
     * @return Model
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function firstOrCreate(array $attributes = [], array $values = []): Model
    {
        if (empty($values)) {
            $values = $attributes;
        }

        $this->prepareQuery();
        $result = $this->model->firstOrCreate($attributes, $values);
        $this->rescueQuery();

        return $result;
    }

    /**
     * Find Or Fail
     *
     * @param int   $id      ID
     * @param array $columns Columns
     *
     * @return Model
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function findOrFail(int $id, array $columns = ['*']): Model
    {
        $this->prepareQuery();
        $result = $this->model->findOrFail($id, $columns);
        $this->rescueQuery();

        return $result;
    }

    /**
     * Where Function
     *
     * @param Closure|null $callback Callback
     *
     * @return $this
     */
    public function whereFunction(Closure $callback = null): static
    {
        $this->model = $this->model->where($callback);

        return $this;
    }

    /**
     * Where Has
     *
     * @param mixed        $relation Relation
     * @param Closure|null $callback Callback
     *
     * @return $this
     */
    public function whereHas(mixed $relation, Closure $callback = null): static
    {
        $this->model = $this->model->whereHas($relation, $callback);

        return $this;
    }

    /**
     * Where Raw
     *
     * @param mixed $query Query
     * @param mixed $value Value
     *
     * @return $this
     */
    public function whereRaw(mixed $query, mixed $value): static
    {
        $this->model = $this->model->whereRaw($query, $value);

        return $this;
    }

    /**
     * Where
     *
     * @param mixed      $column    Column
     * @param string     $condition Condition
     * @param mixed|null $value     Value
     *
     * @return $this
     */
    public function where(string $column, string $condition = '=', mixed $value = null): static
    {
        if (is_null($value)) {
            $value = $condition;
            $condition = '=';
        }

        $condition = strtolower($condition);

        // Switch case
        $this->model = match ($condition) {
            'in' => $this->model->whereIn($column, $value),
            'not_in' => $this->model->whereNotIn($column, $value),
            'like', 'ilike' => $this->model->where($column, $condition, '%' . $value . '%'),
            'is_null' => $this->model->whereNull($column),
            'is_not_null' => $this->model->whereNotNull($column),
            default => $this->model->where($column, $condition, $value),
        };

        return $this;
    }

    /**
     * Where In
     *
     * @param string $column Column
     * @param mixed  $value  Value
     *
     * @return $this
     */
    public function whereIn(string $column, mixed $value): static
    {
        $this->model = $this->model->whereIn($column, $value);

        return $this;
    }

    /**
     * Where Not In
     *
     * @param string $column Column
     * @param mixed  $value  Value
     *
     * @return $this
     */
    public function whereNotIn(string $column, mixed $value): static
    {
        $this->model = $this->model->whereNotIn($column, $value);

        return $this;
    }

    /**
     * Or Where
     *
     * @param string $column Column
     * @param mixed  $value  Value
     *
     * @return $this
     */
    public function orWhere(string $column, mixed $value): static
    {
        $this->model = $this->model->orWhere($column, $value);

        return $this;
    }

    /**
     * Distinct
     *
     * @return $this
     */
    public function distinct(): static
    {
        $this->model = $this->model->distinct();

        return $this;
    }

    /**
     * Where Not Null
     *
     * @param mixed $field Field
     *
     * @return $this
     */
    public function whereNotNull(mixed $field): static
    {
        $this->model = $this->model->whereNotNull($field);

        return $this;
    }

    /**
     * Where Null
     *
     * @param mixed $field
     *
     * @return $this
     */
    public function whereNull(mixed $field): static
    {
        $this->model = $this->model->whereNull($field);

        return $this;
    }

    /**
     * Wheres
     *
     * @param array $wheres Wheres
     *
     * @return $this
     */
    public function wheres(array $wheres = []): static
    {
        foreach ($wheres as $where) {
            $column = $where[0];
            $condition = $where[1];

            if (!isset($where[2])) {
                $value = $condition;
                $condition = '=';
            }

            if (isset($where[2])) {
                $value = $where[2];
            }

            $this->where($column, $condition, $value ?? null);
        }

        return $this;
    }

    /**
     * First
     *
     * @param array $columns Columns
     *
     * @return Model|Builder|\Illuminate\Database\Query\Builder|null
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function first(array $columns = ['*']): Model|Builder|\Illuminate\Database\Query\Builder|null
    {
        $this->prepareQuery();
        $result = $this->model->first($columns);
        $this->rescueQuery();

        return $result;
    }

    /**
     * First Or Fail
     *
     * @param array $columns Columns
     *
     * @return Builder|Model
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function firstOrFail(array $columns = ['*']): Model|Builder
    {
        $this->prepareQuery();
        $result = $this->model->firstOrFail($columns);
        $this->rescueQuery();

        return $result;
    }

    /**
     * Get data of repository
     *
     * @param array $columns Columns
     *
     * @return Builder[]|Collection|\Illuminate\Support\Collection
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function get(array $columns = ['*']): Collection|\Illuminate\Support\Collection|array
    {
        $this->prepareQuery();
        $result = $this->model->get($columns);
        $this->rescueQuery();

        return $result;
    }

    /**
     * Get data of repository by pagination
     *
     * @param int|null $limit   Limit
     * @param array    $columns Columns
     *
     * @return LengthAwarePaginator
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function paginate(int $limit = null, array $columns = ['*']): LengthAwarePaginator
    {
        $this->prepareQuery();
        $result = $this->model->paginate($limit, $columns);
        $this->rescueQuery();

        return $result;
    }

    /**
     * Create new model in repository
     *
     * @param array $attributes Attributes
     *
     * @return Model
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function create(array $attributes): Model
    {
        $result = $this->model->newInstance($attributes);
        $result->save();

        $this->resetModel();

        return $result;
    }

    /**
     * Update the existed model in repository
     *
     * @param int   $id         ID
     * @param array $attributes Attributes
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function update(int $id, array $attributes): mixed
    {
        $this->applyScope();

        $result = $this->model->where('id', $id)->update($attributes);

        $this->resetScope();
        $this->resetModel();

        return $result;
    }

    /**
     * Update All models in repository
     *
     * @param array $attributes Attributes
     *
     * @return bool|int
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function updateAll(array $attributes): bool|int
    {
        $this->applyScope();

        $result = $this->model->update($attributes);

        $this->resetScope();
        $this->resetModel();

        return $result;
    }

    /**
     * Remove the existed model in repository
     *
     * @param int|null $id ID
     *
     * @return boolean
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function delete(int $id = null): bool
    {
        $this->prepareQuery();

        if ($id) {
            $this->model = $this->model->where('id', $id);
        }

        $result = $this->model->delete();
        $this->rescueQuery();

        return $result;
    }

    /**
     * Remove the existed model in repository
     *
     * @return boolean
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function deleteAll(): bool
    {
        $this->prepareQuery();
        $result = $this->model->delete();
        $this->rescueQuery();

        return $result;
    }

    /**
     * Update or Create an entity in repository
     *
     * @param array $attributes Attributes
     * @param array $values     Values
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function updateOrCreate(array $attributes, array $values = []): mixed
    {
        $this->prepareQuery();
        $result = $this->model->updateOrCreate($attributes, $values);
        $this->rescueQuery();

        return $result;
    }

    /**
     * Count records
     *
     * @return int
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function count(): int
    {
        $this->prepareQuery();
        $result = $this->model->count();
        $this->rescueQuery();

        return $result;
    }

    /**
     * Pluck column
     *
     * @param string $column Column
     *
     * @return \Illuminate\Support\Collection
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function pluck(string $column): \Illuminate\Support\Collection
    {
        $this->prepareQuery();
        $result = $this->model->pluck($column);
        $this->rescueQuery();

        return $result;
    }

    /**
     * Insert new records
     *
     * @param array $values Values
     *
     * @return boolean
     */
    public function insert(array $values): bool
    {
        return $this->model->insert($values);
    }

    /**
     * Chunk records
     *
     * @param int     $quantity Quantity
     * @param Closure $callback Callback
     *
     * @return bool
     */
    public function chunk(int $quantity, Closure $callback): bool
    {
        $this->prepareQuery();

        return $this->model->chunk($quantity, $callback);
    }

    /**
     * Determine if any rows exist for the current query.
     *
     * @return bool
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function exists(): bool
    {
        $this->prepareQuery();
        $result = $this->model->exists();
        $this->rescueQuery();

        return $result;
    }

    /**
     * Retrieve the sum of the values of a given column.
     *
     * @param string $column Column
     *
     * @return int|mixed
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function sum(string $column): mixed
    {
        $this->prepareQuery();
        $result = $this->model->sum($column);
        $this->rescueQuery();

        return $result;
    }

    /**
     * Alias to set the “limit” value of the query.
     *
     * @param int $number
     * @return Builder|\Illuminate\Database\Query\Builder
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function take(int $number): Builder|\Illuminate\Database\Query\Builder
    {
        $this->prepareQuery();
        $result = $this->model->take($number);
        $this->rescueQuery();

        return $result;
    }

    /**
     * Add "order by" clause to the query.
     *
     * @param string $column Column
     *
     * @return $this
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function orderBy(string $column): static
    {
        $this->prepareQuery();
        $result = $this->model->orderBy($column);
        $this->rescueQuery();

        return $result;
    }

    /**
     * Add a relationship count / exists condition to the query with where clauses.
     *
     * @param string       $relation Relation
     * @param Closure|null $callback callback
     *
     * @return $this
     */
    public function whereDoesntHave(string $relation, Closure $callback = null): static
    {
        $this->model = $this->model->whereDoesntHave($relation, $callback);

        return $this;
    }
}
