<?php

namespace App\Core\Services;

use App\Core\Criteria\FilterCriteria;
use App\Core\Criteria\OrderCriteria;
use App\Core\Criteria\WithCountRelationsCriteria;
use App\Core\Criteria\WithRelationsCriteria;
use App\Helpers\DateTimeHelper;
use App\Exceptions\BusinessException;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Core\Contracts\RepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
abstract class BaseService
{
    const ACTION_LIST = 'list';
    const ACTION_COUNT = 'count';
    const ACTION_FIND = 'find';
    const ACTION_EXISTS = 'exists';

    protected $data;

    protected $model;

    protected $modelId;

    protected $repository;

    protected $handler;

    protected array $selects = ['*'];
    protected bool $throwable = true;

    /**
     * Handle
     *
     * @return mixed
     */
    abstract public function handle(): mixed;

    /**
     * Set Options
     *
     * @param mixed|null $request Request
     * @param mixed|null $model   Model
     * @param mixed|null $handler Handler
     *
     * @return $this
     */
    public function setOptions(mixed $request = null, mixed $model = null, mixed $handler = null): static
    {
        $data = [];
        if (!is_null($request)) {
            if (is_array($request) || $request instanceof Collection) {
                $data = $request;
            }

            if (is_object($request) && method_exists($request, 'validated')) {
                $data = $request->validated();
            }
        }

        $this->setData($data);

        if (!is_null($model)) {
            $this->setModel($model);
        }

        $this->setHandler($handler);

        return $this;
    }

    /**
     * Set Data
     *
     * @param array $data Data
     *
     * @return $this
     */
    public function setData(array $data = []): static
    {
        $this->data = $data instanceof Collection ? $data : new Collection($data);
        $this->data = $this->data->filter(function ($value) {
            return !is_null($value);
        });

        if ($this->data->has('selects')) {
            $selects = $this->data->get('selects');

            if (is_string($selects)) {
                $explode = explode(',', $selects);
                $selects = [];

                foreach ($explode as $select) {
                    $selects[] = trim(strtolower($select));
                }

                if (!empty($selects)) {
                    $this->selects = $selects;
                }
            }
        }

        if ($this->data->has('throwable')) {
            $this->throwable = $this->data->get('throwable', true);
            $this->data->forget('throwable');
        }

        return $this;
    }

    /**
     * Init Handler
     *
     * @param mixed $handler Handler
     *
     * @return $this
     */
    private function initHandler(mixed $handler): static
    {
        $this->handler = $handler;
        $this->data->forget('handler');

        return $this;
    }

    /**
     * Set Handler
     *
     * @param mixed|null $handler Handler
     *
     * @return $this
     */
    public function setHandler(mixed $handler = null): static
    {
        if (!empty($handler) && $handler instanceof User) {
            return $this->initHandler($handler);
        }

        if (!$this->data->has('handler')
            && Auth::check()) {
            return $this->initHandler(authUser());
        }

        if ($this->data->has('handler')) {
            $handler = $this->data->get('handler');

            if ($handler instanceof User) {
                return $this->initHandler($handler);
            }
        }

        return $this;
    }

    /**
     * Set Model
     *
     * @param mixed $model Model
     *
     * @return $this
     */
    public function setModel(mixed $model): static
    {
        if ($model instanceof Model) {
            $this->model = $model;

            if (!empty($model->id)) {
                $this->modelId = $model->id;
            }
        }

        if (!$model instanceof Model) {
            $this->modelId = $model;
        }

        return $this;
    }

    /**
     * Not Throwable
     *
     * @return $this
     */
    public function notThrowable(): static
    {
        $this->throwable = false;

        return $this;
    }

    /* support data */

    /**
     * Input Id
     *
     * @return mixed
     */
    public function inputId(): mixed
    {
        return $this->modelId ?? $this->data->get('id');
    }

    /**
     * Add With
     *
     * @param string $withs Withs
     *
     * @return void
     */
    public function addWith(string $withs = ''): void
    {
        if (is_string($withs)) {
            $withs = explode(',', $withs);
        }

        $dataWiths = $this->data->get('with', '');

        if (is_string($dataWiths)) {
            $dataWiths = explode(',', $dataWiths);
        }

        if (!empty($withs) && !empty($dataWiths)) {
            $this->data->put('with', implode(',', array_merge($withs, $dataWiths)));
        }
    }

    /**
     * Add With Count
     *
     * @param string $withs Withs
     *
     * @return void
     */
    public function addWithCount(string $withs = ''): void
    {
        if (is_string($withs)) {
            $withs = explode(',', $withs);
        }

        $dataWiths = $this->data->get('with_count', '');

        if (is_string($dataWiths)) {
            $dataWiths = explode(',', $dataWiths);
        }

        if (!empty($withs) && !empty($dataWiths)) {
            $this->data->put('with_count', implode(',', array_merge($withs, $dataWiths)));
        }
    }

    /**
     * Data To Array
     * @param mixed $key
     * @return string[]
     */
    public function dataToArray(mixed $key): array
    {
        $value = $this->data->get($key, '');

        if (is_string($value)) {
            $value = explode(',', $value);
        }

        foreach ($value as &$v) {
            $v = trim($v);
        }

        return $value;
    }

    /**
     * Find Or Check Exists
     *
     * @param RepositoryInterface|null $repository Repository
     *
     * @return null
     */
    public function findOrCheckExists(RepositoryInterface $repository = null)
    {
        if (is_null($repository)) {
            $repository = $this->repository;
        }

        if (is_null($repository)) {
            return null;
        }

        $action = $this->data->get('action', self::ACTION_FIND);

        if ($action == self::ACTION_EXISTS) {
            return $repository->exists();
        }

        $id = $this->inputId();

        if ($this->throwable) {
            return $id
                ? $this->repository->findOrFail($id, $this->selects)
                : $this->repository->firstOrFail($this->selects);
        }

        return $id
            ? $this->repository->find($id, $this->selects)
            : $this->repository->first($this->selects);
    }

    /**
     * Count Or Paginate Or All
     *
     * @param RepositoryInterface|null $repository Repository
     *
     * @return null
     */
    public function countOrPaginateOrAll(RepositoryInterface $repository = null)
    {
        if (is_null($repository)) {
            $repository = $this->repository;
        }

        if (is_null($repository)) {
            return null;
        }

        $action = $this->data->get('action', self::ACTION_LIST);

        if ($action == self::ACTION_COUNT) {
            return $repository->count();
        }

        $limit = 0;

        if ($this->data->has('per_page')) {
            $limit = intval($this->data->get('per_page'));
        }

        if ($this->data->has('limit')) {
            $limit = intval($this->data->get('limit'));
        }

        $limit = max($limit, 0);
        $limit = min($limit, 100);

        return $limit > 0
            ? $repository->paginate($limit, $this->selects)
            : $repository->get($this->selects);
    }

    /**
     * Data Defaults
     *
     * @param array $data Data
     *
     * @return void
     */
    public function dataDefaults(array $data = []): void
    {
        foreach ($data as $key => $value) {
            $this->dataDefault($key, $value);
        }
    }

    /**
     * Data Default
     *
     * @param mixed $key   Key
     * @param mixed $value Value
     *
     * @return void
     */
    public function dataDefault(mixed $key, mixed $value): void
    {
        if (!$this->data->has($key)) {
            $this->data->put($key, $value);
        }
    }

    /**
     * Data To Database Date
     *
     * @param array $keys     Key
     * @param array $converts Converts
     *
     * @return void
     */
    public function dataToDatabaseDate(array $keys = [], array $converts = []): void
    {
        if (is_string($keys)) {
            $keys = explode(',', $keys);
        }

        foreach ($keys as $key) {
            $key = trim($key);

            if ($this->data->has($key)) {
                $value = $this->data->get($key);

                if (empty($value)) {
                    continue;
                }

                $date = DateTimeHelper::toDatabaseDate($value);
                $date = DateTimeHelper::convert($date, $converts);
                $this->data->put($key, $date);
            }
        }
    }

    /**
     * Data To Database Time
     *
     * @param array $keys     Key
     * @param array $converts Converts
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function dataToDatabaseTime(array $keys = [], array $converts = []): void
    {
        if (is_string($keys)) {
            $keys = explode(',', $keys);
        }
        foreach ($keys as $key) {
            $key = trim($key);

            if ($this->data->has($key)) {
                $value = $this->data->get($key);

                if (empty($value)) {
                    continue;
                }

                $time = DateTimeHelper::toDatabaseTime($value, true);
                $time = DateTimeHelper::convert($time, $converts);
                $this->data->put($key, $time);
            }
        }
    }

    /**
     * Data To Lower
     *
     * @param array $keys Keys
     *
     * @return void
     */
    public function dataToLower(array $keys = []): void
    {
        if (is_string($keys)) {
            $keys = explode(',', $keys);
        }

        foreach ($keys as $key) {
            $key = trim($key);

            if ($this->data->has($key)) {
                $value = $this->data->get($key);
                $this->data->put($key, mb_strtolower($value));
            }
        }
    }

    /**
     * Data To Upper
     *
     * @param array $keys Keys
     *
     * @return void
     */
    public function dataToUpper(array $keys = []): void
    {
        if (is_string($keys)) {
            $keys = explode(',', $keys);
        }

        foreach ($keys as $key) {
            $key = trim($key);

            if ($this->data->has($key)) {
                $value = $this->data->get($key);
                $this->data->put($key, mb_strtoupper($value));
            }
        }
    }

    /**
     * Throws exception
     *
     * @param string $error  Error
     * @param string $key    Key
     * @param array  $params Params
     *
     * @return mixed
     * @throws ValidationException
     */
    public function exception(string $error, string $key = '', array $params = []): mixed
    {
        if (!empty($key)) {
            throw ValidationException::withMessages([
                $key => [__('messages.'.$error, $params)]
            ]);
        }

        throw BusinessException::$error($params);
    }

    /**
     * Find Common
     *
     * @return null
     */
    public function findCommon()
    {
        $this->repository->pushCriteria(new FilterCriteria($this->data->toArray()));
        $this->repository->pushCriteria(new WithRelationsCriteria($this->data->get('with')));
        $this->repository->pushCriteria(new WithCountRelationsCriteria($this->data->get('with_count')));

        return $this->findOrCheckExists();
    }

    /**
     * List Common
     *
     * @return null
     */
    public function listCommon()
    {
        $this->repository->pushCriteria(new FilterCriteria($this->data->toArray()));
        $this->repository->pushCriteria(new WithRelationsCriteria($this->data->get('with')));
        $this->repository->pushCriteria(new WithCountRelationsCriteria($this->data->get('with_count')));
        $this->repository->pushCriteria(new OrderCriteria($this->data->get('order', '-id')));

        return $this->countOrPaginateOrAll();
    }
}
