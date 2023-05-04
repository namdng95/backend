<?php

namespace App\Http\Requests\Password;

use App\Core\Requests\BaseRequest;

class ResetPasswordRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'password' => [
                'required',
                'string',
                'confirmed',
                'max:8',
                'max:30'
            ],
            'password_confirmation' => [
                'required',
                'string',
                'max:8',
                'max:30'
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
            'password.confirmed' => __('messages.validation.password.password.confirmed'),
            'password.required'  => __('messages.validation.password.password.required'),
            'password.string'    => __('messages.validation.password.password.string'),
            'password.min'       => __('messages.validation.password.password.min'),
            'password.max'       => __('messages.validation.password.password.max'),

            'password_confirmation.required' => __('messages.validation.password.password_confirmation.required'),
            'password_confirmation.string'   => __('messages.validation.password.password_confirmation.string'),
            'password_confirmation.min'      => __('messages.validation.password.password_confirmation.min'),
            'password_confirmation.max'      => __('messages.validation.password.password_confirmation.max'),
        ];
    }
}
