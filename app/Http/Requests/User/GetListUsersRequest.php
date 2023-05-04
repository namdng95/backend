<?php

namespace App\Http\Requests\User;

use App\Core\Requests\BaseRequest;

class GetListUsersRequest extends BaseRequest
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
