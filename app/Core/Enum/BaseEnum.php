<?php

namespace App\Core\Enum;

use BenSampo\Enum\Enum;

/** @SuppressWarnings(PHPMD.NumberOfChildren) */
class BaseEnum extends Enum
{
    /**
     * As String
     *
     * @return string
     */
    public static function asString(): string
    {
        $values = self::asArray();

        return implode(',', $values);
    }

    /**
     * As Select Text
     *
     * @return array
     */
    public static function asSelectText(): array
    {
        $roles = self::asSelectArray();

        foreach ($roles as $k => $v) {
            $roles[$k] = __('messages.' .$v);
        }

        return $roles;
    }

    /**
     * As Array With Keys
     *
     * @return array
     */
    public static function asArrayWithKeys(): array
    {
        $roles = self::asSelectArray();
        $result = [];

        foreach ($roles as $k => $v) {
            $result[$v] = $k;
        }

        return $result;
    }

    /**
     * Get text
     *
     * @param string $value Value
     *
     * @return mixed
     */
    public static function text(string $value): mixed
    {
        $roles = self::asSelectText();

        return $roles[$value] ?? __('messages.' . $value);
    }

    /**
     * Get key
     *
     * @param string $string String
     *
     * @return int|string|null
     */
    public static function key(string $string): int|string|null
    {
        $roles = self::asSelectArray();

        foreach ($roles as $k => $value) {
            if (trim(strtolower($value)) == trim(strtolower($string))) {
                return $k;
            }
        }

        return null;
    }
}
