<?php

namespace App\Core\Filters;

use App\Core\Contracts\FilterInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/** @SuppressWarnings(PHPMD.NumberOfChildren) */
abstract class BaseFilter implements FilterInterface
{
    const DEFAULT_TIMEZONE = 'Asia/Ho_Chi_Minh';

    /**
     * @var Collection
     */
    protected static Collection $grabInputs;

    /**
     * @var array
     */
    protected static array $optionalInputs = [];

    /**
     * Grab other input for using in the filter.
     *
     * @param Collection $data
     */
    public static function grabInputs(Collection $data): void
    {
        self::$grabInputs = $data->only(self::$optionalInputs);
    }

    /**
     * Escape
     *
     * @param string $string String
     *
     * @return string
     */
    public static function escape(string $string): string
    {
        $string = str_replace('\\', '\\\\', mb_strtolower($string));

        return addcslashes($string, '%_');
    }

    /**
     * Apply the filter
     *
     * @param Model $model      Model
     * @param mixed $input      Input
     *
     * @return Model|Builder|\Illuminate\Database\Query\Builder
     */
    public static function apply(Model $model, mixed $input): Model|Builder|\Illuminate\Database\Query\Builder
    {
        return $model;
    }
}
