<?php

namespace App\Http\Requests\Password;

use App\Core\Requests\BaseRequest;

class ChangePasswordRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'string',
                'min:8',
                'max:30'
            ],
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
            'current_password.required' => __('messages.validation.password.current_password.required'),
            'current_password.string'   => __('messages.validation.password.current_password.string'),
            'current_password.min'      => __('messages.validation.password.current_password.min'),
            'current_password.max'      => __('messages.validation.password.current_password.max'),

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
