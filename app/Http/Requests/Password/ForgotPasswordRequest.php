<?php

namespace App\Http\Requests\Password;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ForgotPasswordRequest extends BaseRequest
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
                Rule::exists('users', 'email')->whereNull('deleted_at')
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
        return [
            'email.required'  => __('messages.validation.password.email.required'),
            'email.string'    => __('messages.validation.password.email.string'),
            'email.regex'       => __('messages.validation.password.email.regex'),
            'email.exists'       => __('messages.validation.password.email.exists'),
        ];
    }
}
