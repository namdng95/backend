<?php

namespace App\Services\User\Tasks;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use App\Repositories\UserRepository;
use App\Services\Task;
use Throwable;

class GetListUsersTask extends Task
{
    /**
     * UserRepository
     *
     * @var UserRepository
     */
    protected UserRepository $repository;

    /**
     * Construct.
     *
     * @return void
     */
    public function __construct()
    {
        $this->repository = resolve(UserRepository::class);
    }

    /**
     *  Count records
     *
     * @param array $data Data
     *
     * @return int
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function count(array $data): int
    {
        $query = $this->prepareQuery($data);

        return $query->count();
    }

    /**
     * Execute task
     *
     * @param array $data Data
     *
     * @return mixed
     */
    public function handle(array $data): mixed
    {
        $query = $this->prepareQuery($data);

        return $this->getList($query, $data);
    }

    /**
     * Prepare query
     *
     * @param mixed $data Data
     *
     * @return UserRepository|Application|mixed
     */
    public function prepareQuery(mixed $data): mixed
    {
        $query = $this->repository;

        if (hasSearch($data, 'company_id')) {
            $query = $query->where('company_id', $data['company_id']);
        }

        return $query;
    }
}
