<?php

namespace App\Core\Exceptions;

use Illuminate\Support\Str;
use Exception;
use Throwable;

/** @SuppressWarnings(PHPMD.NumberOfChildren) */
abstract class BaseException extends Exception
{
    /**
     * @var int
     */
    protected $code = 400;

    /**
     * @var string|null
     */
    protected ?string $messageCode = null;

    /**
     * Construct.
     *
     * @param string         $message  Message
     * @param int            $code     Code
     * @param Throwable|null $previous Previous
     */
    final public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get prefix of code
     *
     * @return string
     */
    abstract protected static function getPrefix(): string;

    /**
     * Get list of allowable methods
     *
     * @return array<string>
     */
    protected static function getAllowableMethods(): array
    {
        return [];
    }

    /**
     * Set the message code
     *
     * @param string $code
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
     * Call Static
     *
     * @param string $name      Name
     * @param array  $arguments Arguments
     *
     * @return BaseException
     */
    public static function __callStatic(string $name, array $arguments)
    {
        $code = static::getPrefix() . '.' . Str::snake($name);

        return (new static(__('messages.' . $code, $arguments[1] ?? []), $arguments[0] ?? 0))->setMessageCode($code);
    }
}
