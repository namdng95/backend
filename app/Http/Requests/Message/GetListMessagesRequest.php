<?php

namespace App\Http\Requests\Message;

use App\Core\Requests\BaseRequest;

class GetListMessagesRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->commonListRules();
    }
}
