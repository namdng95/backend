<?php

namespace App\Http\Requests\User;

use App\Core\Requests\BaseRequest;
use App\Enums\User\UserStatus;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'regex:' . self::UUID_REGEX,
                'max:50',
                Rule::unique('users', 'code')
                    ->whereNull('deleted_at')
                    ->ignore(self::getId('user'))
            ],
            'name' => [
                'required',
                'string',
                'between:2,100',
            ],
            'email' => [
                'required',
                'max:256',
                Rule::unique('users', 'email')
                    ->whereNull('deleted_at')
                    ->ignore(self::getId('user')),
                'regex:' . self::EMAIL_REGEX
            ],
            'password' => [
                'required',
                'min:8',
                'max:30'
            ],
            'status' => [
                'nullable',
                'integer',
                'in: ' . UserStatus::asString()
            ],
        ];
    }

    /**
     * Error messages
     *
     * @return array
     */
    public function messages(): array
    {
        return self::userErrorMessages();
    }
}
