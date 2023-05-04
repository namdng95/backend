<?php

namespace App\Services\User\Tasks;

use Illuminate\Contracts\Container\BindingResolutionException;
use App\Repositories\UserRepository;
use App\Services\Task;
use Throwable;

class CreateUserTask extends Task
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
