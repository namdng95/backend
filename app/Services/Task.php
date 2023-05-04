<?php

namespace App\Services;

use App\Core\Criteria\WithCountRelationsCriteria;
use App\Core\Criteria\WithRelationsCriteria;
use App\Core\Criteria\OrderCriteria;
use App\Exceptions\BusinessException;
use App\Enums\Common\BooleanTypes;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class Task
{
    /**
     * Throw exception
     *
     * @param string $error    Error
     * @param string $key      Key
     * @param array  $params   Params
     * @param int    $httpCode Http Code
     *
     * @return mixed
     * @throws ValidationException
     */
    public function exception(string $error, string $key = '', array $params = [], int $httpCode = Response::HTTP_BAD_REQUEST): mixed
    {
        if (!empty($key)) {
            throw ValidationException::withMessages([
                $key => __('messages.' . $error)
            ]);
        }

        throw BusinessException::$error($params, $httpCode);
    }

    /**
     * Throw exceptions
     *
     * @param array $errors Errors
     *
     * @return void
     * @throws ValidationException
     */
    public function exceptions(array $errors = []): void
    {
        $errorMessages = [];

        foreach ($errors as $key => $error) {
            $errorMessages[$key] = __('messages.' . $error);
        }

        if (!empty($errorMessages)) {
            throw ValidationException::withMessages($errorMessages);
        }
    }

    /**
     * Throw exception permission
     *
     * @return void
     * @throws ValidationException
     */
    public function exceptionPermission(): void
    {
        $this->exception('permission_denied', '', [], Response::HTTP_FORBIDDEN);
    }

    /**
     * Is encode column
     *
     * @param string $columnName Column Name
     *
     * @return bool
     */
    public function isEncodeColumn(string $columnName): bool
    {
        $last5chars = substr($columnName, -5);

        return in_array($last5chars, ['uuid', 'code', '.uuid', '.code']);
    }

    /**
     * Query search like
     *
     * @param mixed   $query        Query
     * @param string  $value        Value
     * @param array   $columns      Columns
     * @param boolean $isRepository Is repository
     *
     * @return mixed
     */
    public function querySearchLike(mixed $query, string $value = '', array $columns = [], bool $isRepository = true): mixed
    {
        if (!hasSearch($value) || empty($columns)) {
            return $query;
        }

        if (is_string($columns)) {
            $columns = [$columns];
        }

        if (!is_array($columns)) {
            return $query;
        }

        if ($isRepository) {
            return $query->whereFunction(function ($subQuery) use ($columns, $value) {
                return $this->querySearchRaw($subQuery, $columns, $value);
            });
        }

        return $query->where(function ($subQuery) use ($columns, $value) {
            return $this->querySearchRaw($subQuery, $columns, $value);
        });
    }

    /**
     * Query search raw
     *
     * @param mixed  $subQuery Sub query
     * @param array  $columns  Columns
     * @param string $value    Value
     *
     * @return mixed
     */
    public function querySearchRaw(mixed $subQuery, array $columns = [], string $value = ''): mixed
    {
        $valueUuid = escapeStringUuid($value);
        $valueString = escapeString($value);

        foreach ($columns as $column) {
            $value = $this->isEncodeColumn($column)
                ? $valueUuid
                : $valueString;
            $subQuery = $subQuery->orWhereRaw('lower(' . $column . ') like (?)', "%{$value}%");
        }

        return $subQuery;
    }

    /**
     * Query search with check all
     *
     * @param array  $data     Data
     * @param string $key      Key
     * @param mixed  $query    Query
     * @param string $relation Relation
     * @param string $fieldIn  Field In
     *
     * @return mixed
     */
    public function querySearchWithCheckAll(array $data, string $key, mixed $query, string $relation = '', string $fieldIn = ''): mixed
    {
        $ids = convertToArray($data[$key . '_ids'] ?? '');
        $checkAll = ($data[$key . '_check_all'] ?? BooleanTypes::FALSE) == BooleanTypes::TRUE;

        if (empty($ids) && !$checkAll) {
            return $query;
        }

        if (empty($relation)) {
            if (!$checkAll) {
                $query = $query->whereIn($fieldIn, $ids);
                if (in_array(0, $ids)) {
                    $query = $query->orWhereNull($fieldIn);
                }
            }
            if ($checkAll) {
                $query = $query->whereNotIn($fieldIn, $ids);
            }
            return $query;
        }

        $query = $query->whereHas($relation, function ($sQ) use ($ids, $checkAll, $fieldIn) {
            if (!$checkAll) {
                $sQ = $sQ->whereIn($fieldIn, $ids);
            }

            if ($checkAll) {
                $sQ = $sQ->whereNotIn($fieldIn, $ids);
            }

            return $sQ;
        });

        if (($checkAll && !in_array(0, $ids))
            || (!$checkAll && in_array(0, $ids))) {
            $query = $query->orWhereDoesntHave($relation);
        }

        return $query;
    }

    /**
     * Load criteria
     *
     * @param mixed $query Query
     * @param array $data  Data
     *
     * @return mixed
     */
    public function loadCriteria(mixed $query, array $data = []): mixed
    {
        if (!empty($data['with'])) {
            $query = $query->pushCriteria(new WithRelationsCriteria($data['with']));
        }

        if (!empty($data['with_count'])) {
            $query = $query->pushCriteria(new WithCountRelationsCriteria($data['with_count']));
        }

        if (!empty($data['order'])) {
            $query = $this->pushOrder($query, $data['order']);
        }

        return $query;
    }

    /**
     * Get List
     *
     * @param mixed $query Query
     * @param array $data  Data
     *
     * @return mixed
     */
    public function getList(mixed $query, array $data = []): mixed
    {
        $data['order'] = $data['order'] ?? '-id';
        $query = $this->loadCriteria($query, $data);

        $limit = $data['limit'] ?? 0;
        $limit = max($limit, 0);
        $limit = min($limit, 200);

        if (!empty($data['take'])) {
            return $query->take($data['take'])->get();
        }

        return $limit > 0
            ? $query->paginate($limit)
            : $query->get();
    }

    /**
     * Push new criteria to the stack
     *
     * @param mixed $query Query
     * @param mixed $order Order
     *
     * @return mixed
     */
    public function pushOrder(mixed $query, mixed $order): mixed
    {
        $orders = convertToArray($order);
        $joined = [];

        foreach ($orders as $ind => $order) {
            $order = ltrim($order, '-');
            $explode = explode('.', $order);

            if (count($explode) > 1) {
                for ($i = 0; $i < count($explode) - 1; $i++) {
                    $table = $explode[$i];
                    if (!in_array($table, $joined)) {
                        $joined[] = $table;
                        $query = $query->joinTable($table);
                    }
                }
            }

            if (count($explode) > 2) {
                $orders[$ind] = $orders[$ind] == $order ? '' : '-';
                $orders[$ind] .= $explode[count($explode) - 2] . '.' . $explode[count($explode) - 1];
            }
        }

        return $query->pushCriteria(new OrderCriteria($orders));
    }

    /**
     * Get detail
     *
     * @param mixed    $query Query
     * @param array    $data  Data
     * @param int|null $id    Id
     *
     * @return mixed
     */
    public function getDetail(mixed $query, array $data = [], int $id = null): mixed
    {
        $query = $this->loadCriteria($query, $data);

        if (!empty($data['no_throw'])) {
            return $id
                ? $query->find($id)
                : $query->first();
        }

        return $id
            ? $query->findOrFail($id)
            : $query->firstOrFail();
    }

    /**
     * Query in
     *
     * @param mixed  $query    Query
     * @param string $column   Column
     * @param mixed  $values   Values
     * @param string $relation Relation
     *
     * @return mixed
     */
    public function queryIn(mixed $query, string $column, mixed $values = '', string $relation = ''): mixed
    {
        if ($values === '' || $values === []) {
            return $query;
        }

        if (!empty($relation)) {
            return $query->whereHas($relation, function ($q) use ($column, $values) {
                return $this->queryIn($q, $column, $values);
            });
        }

        if (!is_array($values)) {
            $values = convertToArray($values);
        }

        return $query->whereIn($column, $values);
    }
}
