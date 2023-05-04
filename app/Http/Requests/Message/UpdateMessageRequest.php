<?php

namespace App\Http\Requests\Message;

use App\Core\Requests\BaseRequest;

class UpdateMessageRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'message' => [
                'required',
                'string',
                'max:256'
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
        return self::messageErrorMessages();
    }
}
