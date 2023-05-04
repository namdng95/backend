<?php

namespace App\Services\Message\Tasks;

use Illuminate\Contracts\Container\BindingResolutionException;
use App\Repositories\MessageRepository;
use App\Services\Task;
use Throwable;

class DeleteMessageTask extends Task
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
     * @param int|null $id ID
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function handle(int $id = null): mixed
    {
        return $this->repository->delete($id);
    }
}
