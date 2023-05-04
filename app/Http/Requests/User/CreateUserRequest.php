<?php

namespace App\Http\Requests\User;

use App\Core\Requests\BaseRequest;
use App\Enums\User\UserStatus;
use Illuminate\Validation\Rule;

class CreateUserRequest extends BaseRequest
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
                    ->whereNull('deleted_at'),
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
            'file' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,bmp',
                'max:15360',
            ]
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
