<?php

namespace App\Services\Password\Actions;

use Illuminate\Validation\ValidationException;
use App\Services\User\Tasks\GetUserDetailTask;
use App\Services\Action;

class ForgotPasswordAction extends Action
{
    /**
     * Execute action.
     *
     * @param array $data Data
     *
     * @return array
     * @throws ValidationException
     */
    public function handle(array $data = []): array
    {
        $webUrl = config('custom.web_url') ?? config('app.url');

        $data['url'] = trim($webUrl, '/');

        $user = (new GetUserDetailTask())->handle(null, $data);

        if (!$user) {
            $this->exception('password.email');
        }

        $user->update([
            'forgotten_password_code' => sha1(md5(rand(1000, 9000))),
            'forgotten_password_time' => time()
        ]);

        $urlReset = $data['url'] . '/password/reset/' . $user->forgotten_password_code;

        return [
            'url' => $urlReset
        ];
    }
}
