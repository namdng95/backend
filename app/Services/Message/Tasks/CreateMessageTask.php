<?php

namespace App\Services\Message\Tasks;

use Illuminate\Contracts\Container\BindingResolutionException;
use App\Repositories\MessageRepository;
use App\Services\Task;
use Throwable;

class CreateMessageTask extends Task
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
     * @param array $data Data
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function handle(array $data): mixed
    {
        return $this->repository->create($data);
    }
}
