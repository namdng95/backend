<?php

namespace App\Services\Message\Tasks;

use App\Repositories\MessageRepository;
use App\Services\Task;

class GetMessageDetailTask extends Task
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
     * Execute task
     *
     * @param int   $id   ID
     * @param array $data Data
     *
     * @return mixed
     */
    public function handle(int $id, array $data): mixed
    {
        $query = $this->prepareQuery($data);

        return $this->getDetail($query, $data, $id);
    }

    /**
     * Prepare query
     *
     * @param mixed $data Data
     *
     * @return mixed
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
