<?php

namespace App\Services\User\Actions;

use App\Events\SendMailNotificationsEvent;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Hash;
use App\Services\User\SubActions\SaveLogoUserSubAction;
use App\Services\User\Tasks\CreateUserTask;
use App\Http\Resources\User\UserResource;
use App\Jobs\SendZipFileEmailJob;
use App\Enums\User\UserStatus;
use App\Services\Action;
use Throwable;

class CreateUserAction extends Action
{
    /**
     * Execute action
     *
     * @param array $data Data
     *
     * @return UserResource
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function handle(array $data = []): UserResource
    {
        $dataFile = (new SaveLogoUserSubAction())->handle($data);

        dispatch(new SendZipFileEmailJob($dataFile));
//        SendZipFileEmailJob::dispatch($dataFile);

        $dataUser = [
            'name'     => $data['name'],
            'code'     => $data['code'] ,
            'email'    => $data['email'],
            'logo'     => $dataFile['file_path'] ?? '',
//            'password' => bcrypt($data['password']), // using for sanctum auth
            'password' => Hash::make($data['password']), // using for JWT auth
            'status'   => $data['status'] ?? UserStatus::ENABLE
        ];

        $user = (new CreateUserTask())->handle($dataUser);

        event(new SendMailNotificationsEvent($user));

        return (new UserResource($user));
    }
}
