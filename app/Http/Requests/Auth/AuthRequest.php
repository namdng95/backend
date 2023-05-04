<?php

namespace App\Http\Requests\Auth;

use App\Core\Requests\BaseRequest;

class AuthRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'regex:' . self::EMAIL_REGEX,
                'max:256'
            ],
            'password' => [
                'required',
                'string',
                'max:256'
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
        return [
            'email.required' => __('messages.validation.auth.email.required'),
            'email.string'   => __('messages.validation.auth.email.string'),
            'email.regex'    => __('messages.validation.auth.email.regex'),
            'email.max'      => __('messages.validation.auth.email.max'),

            'password.required' => __('messages.validation.auth.password.required'),
            'password.string'   => __('messages.validation.auth.password.string'),
            'password.max'      => __('messages.validation.auth.password.max'),
        ];
    }
}
