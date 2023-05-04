<?php

namespace App\Enums\Common;

use App\Core\Enum\BaseEnum;

final class ProductTax extends BaseEnum
{
    const NONE = 0;
    const TEN_PERCENT = 1;
    const EIGHT_PERCENT = 2;
    const DUTY_FREE = 3;
    const TAX_EXEMPT = 4;
    const TAX_FEE = 5;

    /**
     * Get text of value
     *
     * @param int $value Value
     *
     * @return mixed|null
     */
    public static function get(int $value): mixed
    {
        $data = [
            self::NONE => [
                'id' => self::NONE,
                'value' => 0,
                'text' => ''
            ],
            self::TEN_PERCENT => [
                'id' => self::TEN_PERCENT,
                'value' => 10,
                'text' => '10%'
            ],
            self::EIGHT_PERCENT => [
                'id' => self::EIGHT_PERCENT,
                'value' => 8,
                'text' => '8%（軽減税率）'
            ],
            self::DUTY_FREE => [
                'id' => self::DUTY_FREE,
                'value' => 0,
                'text' => '免税'
            ],
            self::TAX_EXEMPT => [
                'id' => self::TAX_EXEMPT,
                'value' => 0,
                'text' => '非課税'
            ],
            self::TAX_FEE => [
                'id' => self::TAX_FEE,
                'value' => 0,
                'text' => '不課税'
            ],
        ];

        return $data[$value] ?? null;
    }

    /**
     * Calculate tax
     *
     * @param int $taxId  Tax ID
     * @param float $price  Price
     * @param bool  $isHuge Is huge number
     *
     * @return float|int|string
     */
    public static function calculateTax(int $taxId, float $price, bool $isHuge = true): float|int|string
    {
        $tax = self::get($taxId);
        $rate = $tax['value'] / 100;

        if ($isHuge) {
            return calculateHugeNumber($price, $rate, '*');
        }

        return $price * $rate;
    }

    /**
     * Show
     *
     * @param int $value Value
     *
     * @return string
     */
    public static function show(int $value): string
    {
        $rate = self::get($value);

        return $rate['value'] . '%';
    }
}
