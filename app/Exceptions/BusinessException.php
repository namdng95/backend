<?php

namespace App\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Exception;
use Throwable;

class BusinessException extends Exception
{
    /**
     * @var string|null
     */
    protected ?string $messageCode = null;

    /**
     * Set the message code
     *
     * @param string $code Code
     *
     * @return self
     */
    public function setMessageCode(string $code): static
    {
        $this->messageCode = $code;

        return $this;
    }

    /**
     * Get the message code
     *
     * @return string|null
     */
    public function getMessageCode(): ?string
    {
        return $this->messageCode;
    }

    /**
     * Convert Error Code
     *
     * @param string $errorCode Error Code
     *
     * @return string
     */
    public static function convertErrorCode(string $errorCode): string
    {
        $errorCode = strtolower(Str::snake($errorCode));

        if (str_starts_with($errorCode, 'errors.')) {
            $errorCode = substr($errorCode, 7);
        } elseif (str_starts_with($errorCode, 'messages.')) {
            $errorCode = substr($errorCode, 9);
        }

        return $errorCode;
    }

    /**
     * Construct.
     *
     * @param string         $errorCode Error Code
     * @param array          $params    Params
     * @param int            $httpCode  Http Code
     * @param Throwable|null $previous  Previous
     */
    public function __construct(
        string $errorCode = 'server_error',
        array $params = [],
        $httpCode = Response::HTTP_BAD_REQUEST,
        Throwable $previous = null
    )
    {
        $errorCode = self::convertErrorCode($errorCode);
        $this->setMessageCode('errors.'.$errorCode);
        $message = __('messages.'.$errorCode, $params);

        if ($errorCode == 'server_error') {
            $httpCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        parent::__construct($message, $httpCode, $previous);
    }

    /**
     * Call Static
     *
     * @param string $errorCode Error Code
     * @param array  $arguments Arguments
     *
     * @return static
     */
    public static function __callStatic(string $errorCode, array $arguments)
    {
        $errorCode = self::convertErrorCode($errorCode);
        $params = $arguments[0] ?? [];

        if (!is_array($params)) {
            $params = [];
        }

        $httpCode = $arguments[1] ?? Response::HTTP_BAD_REQUEST;

        return (new static($errorCode, $params, $httpCode));
    }
}
