<?php

namespace App\Core\Rules;

use Illuminate\Contracts\Validation\Rule;

class WebsiteUrl implements Rule
{
    protected $regex = '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w\.-]*)*\/?/';

    /**
     * @var int
     */
    protected $max = 255;
    /**
     * @var int
     */
    protected $min = 16;

    /**
     * @var string
     */
    protected $message;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function passes($attribute, $value)
    {
        if (! is_string($value)) {
            $this->message = __('validation.string', ['attribute' => $this->__toString()]);
        } elseif ($this->min > strlen($value)) {
            $this->message = __('validation.min.string', ['attribute' => $this->__toString(), 'min' => $this->min]);
        } elseif ($this->max < strlen($value)) {
            $this->message = __('validation.max.string', ['attribute' => $this->__toString(), 'max' => $this->max]);
        } elseif (! preg_match($this->regex, $value)) {
            $this->message = __('validation.regex', ['attribute' => $this->__toString()]);
        }

        return empty($this->message);
    }

    /**
     * Get the validation error message
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }

    public function __toString()
    {
        return __('validation.attributes.url');
    }

    /**
     * Set min characters
     *
     * @param int $max
     */
    public function setMax(int $max)
    {
        $this->max = $max;
    }

    /**
     * Set regex
     *
     * @param string $regex
     */
    public function setRegex(string $regex)
    {
        $this->regex = $regex;
    }
}
