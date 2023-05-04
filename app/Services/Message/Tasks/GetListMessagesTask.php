<?php

namespace App\Services\Message\Tasks;

use App\Repositories\MessageRepository;
use App\Repositories\UserRepository;
use App\Services\Task;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Throwable;

class GetListMessagesTask extends Task
{
    /**
     * Message Repository
     *
     * @var MessageRepository
     */
    protected MessageRepository $repository;

    /**
     * Construct.
     *
     * @return void
     */
    public function __construct()
    {
        $this->repository = resolve(MessageRepository::class);
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
