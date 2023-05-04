<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class Action
{
    /**
     * Throw exception.
     *
     * @param string $error    Error
     * @param string $key      Key
     * @param array  $params   Params
     * @param int    $httpCode HTTP Code
     *
     * @return mixed
     * @throws ValidationException
     */
    public function exception(string $error, string $key = '', array $params = [], int $httpCode = Response::HTTP_BAD_REQUEST): mixed
    {
        if (!empty($key)) {
            throw ValidationException::withMessages([
                $key => __('messages.' . $error)
            ]);
        }

        throw BusinessException::$error($params, $httpCode);
    }

    public function exceptionPermission()
    {
        $this->exception('permission_denied', '', [], Response::HTTP_FORBIDDEN);
    }

    public function exceptions($errors = [])
    {
        $errorMessages = [];
        foreach ($errors as $key => $error) {
            $errorMessages[$key] = __('messages.' . $error);
        }
        if (!empty($errorMessages)) {
            throw ValidationException::withMessages($errorMessages);
        }
    }

    public function catchError($e, $message)
    {
        if (method_exists($e, 'getMessage')) {
            logger()->debug($e->getMessage());
        }
        if ($e instanceof BusinessException || $e instanceof ValidationException) {
            throw $e;
        }
        $this->exception($message);
    }

    public function setDataDefault($data = [], $defaults = [])
    {
        foreach ($defaults as $param => $default) {
            if (($data[$param] ?? '') === '') {
                $data[$param] = $default ;
            }
        }

        return $data;
    }
}
