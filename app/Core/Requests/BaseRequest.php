<?php

namespace App\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** @SuppressWarnings(PHPMD.NumberOfChildren) */
class BaseRequest extends FormRequest
{
    const UUID_REGEX = "/^[a-zA-Z0-9]{1,255}$/";
    const EMAIL_REGEX = "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/";

    /**
     * Common list rules
     *
     * @return array
     */
    public function commonDetailRules(): array
    {
        return [
            'with' => [
                'nullable',
                'string',
            ],
            'with_count' => [
                'nullable',
                'string',
            ]
        ];
    }

    /**
     * Common list rules
     *
     * @return array
     */
    public function commonListRules(): array
    {
        return array_merge(self::commonDetailRules(), [
            'page' => [
                'nullable',
                'integer',
            ],
            'limit' => [
                'nullable',
                'integer',
                'min:1'
            ],
            'order' => [
                'nullable',
                'string',
            ],
        ]);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get Id from key
     *
     * @param string $key Key
     *
     * @return mixed
     */
    public static function getId(string $key = ''): mixed
    {
        $id = request('id');

        if (!empty($id)) {
            return $id;
        }

        $object = request($key);

        if (is_numeric($object)) {
            return $object;
        }

        return optional($object)->id;
    }

    /**
     * User Error Messages
     *
     * @return array
     */
    public static function userErrorMessages(): array
    {
        return [
            'code.required' => __('messages.validation.user.code.required'),
            'code.string'   => __('messages.validation.user.code.string'),
            'code.max'      => __('messages.validation.user.code.max'),
            'code.regex'    => __('messages.validation.user.code.regex'),
            'code.unique'   => __('messages.validation.user.code.unique'),

            'name.required' => __('messages.validation.user.name.required'),
            'name.string'   => __('messages.validation.user.name.string'),
            'name.between'  => __('messages.validation.user.name.between'),

            'email.required' => __('messages.validation.user.email.required'),
            'email.max'      => __('messages.validation.user.email.max'),
            'email.unique'   => __('messages.validation.user.email.unique'),
            'email.regex'    => __('messages.validation.user.email.regex'),

            'password.required' => __('messages.validation.user.password.required'),
            'password.min'      => __('messages.validation.user.password.min'),
            'password.max'      => __('messages.validation.user.password.max'),

            'status.integer' => __('messages.validation.user.status.integer'),
            'status.in'      => __('messages.validation.user.status.in'),
        ];
    }

    /**
     * Message Error Messages
     *
     * @return array
     */
    public static function messageErrorMessages(): array
    {
        return [
            'message.required' => __('messages.validation.message.message.required'),
            'message.string'   => __('messages.validation.message.message.string'),
            'message.max'      => __('messages.validation.message.message.max'),
        ];
    }
}
