<?php

namespace App\Services\User\Tasks;

use Illuminate\Contracts\Container\BindingResolutionException;
use App\Repositories\UserRepository;
use App\Services\Task;
use Throwable;

class DeleteUserTask extends Task
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
