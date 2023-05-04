<?php

namespace App\Core\Rules;

use Illuminate\Contracts\Validation\Rule;

class Phone implements Rule
{
    /**
     * @var string
     */
    protected $regex = "/^0[\d]{9,10}$/";

    /**
     * @var int
     */
    protected $min = 10;

    /**
     * @var int
     */
    protected $max = 11;

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
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }

    public function __toString()
    {
        return __('validation.attributes.phone');
    }

    /**
     * Set min characters
     *
     * @param int $min
     */
    public function setMin(int $min)
    {
        $this->min = $min;
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
