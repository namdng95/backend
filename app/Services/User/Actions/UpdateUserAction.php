<?php

namespace App\Services\User\Actions;

use App\Enums\User\UserStatus;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Hash;
use App\Services\Action;
use Throwable;

class UpdateUserAction extends Action
{
    /**
     * Execute action
     *
     * @param int|null $id   ID
     * @param array    $data Data
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function handle(int $id = null, array $data = []): mixed
    {
        $user = (new GetUserDetailAction())->handle($id);

        $dataUser = [
            'name'     => $data['name'],
            'code'     => $data['code'] ,
            'email'    => $data['email'] ,
//            'password' => bcrypt($data['password']), // using for sanctum auth
            'password' => Hash::make($data['password']), // using for JWT auth
            'status'   => $data['status'] ?? UserStatus::ENABLE
        ];

        $user->update($dataUser);

        return $user->refresh();
    }
}
