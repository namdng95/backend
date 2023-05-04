<?php

namespace App\Services\User\Tasks;

use App\Repositories\UserRepository;
use App\Services\Task;

class GetUserDetailTask extends Task
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
     * @param int|null $id   ID
     * @param array    $data Data
     *
     * @return mixed
     */
    public function handle(int $id = null, array $data): mixed
    {
        $query = $this->prepareQuery($data);

        return $this->getDetail($query, $data, $id);
    }

    /**
     * Prepare query
     *
     * @param array $data Data
     *
     * @return mixed
     */
    public function prepareQuery(array $data): mixed
    {
        $query = $this->repository;

        if (hasSearch($data, 'email')) {
            $query = $query->where('email', $data['email']);
        }

        if (hasSearch($data, 'forgot_password_code')) {
            $query = $query->where('forgot_password_code', $data['forgot_password_code']);
        }

        return $query;
    }
}
