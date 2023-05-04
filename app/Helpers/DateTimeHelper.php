<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class DateTimeHelper
{
    /**
     * Allow Formats
     *
     * @return string[]
     */
    public static function allowFormats(): array
    {
        return [
            'd-m-Y' => 'd-m-Y',
            'd/m/Y' => 'd/m/Y',
            'd.m.Y' => 'd.m.Y',
            'Y-m-d' => 'Y-m-d',
            'Y/m/d' => 'Y/m/d',
            'Y.m.d' => 'Y.m.d',
        ];
    }

    /**
     * Current Timezone
     *
     * @return array|Repository|Application|mixed|string|string[]
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function currentTimezone(): mixed
    {
        $timezone = session()->get('timezone', config('app.timezone'));

        if (request()->hasHeader('timezone')) {
            $timezone = strtoupper(request()->header('timezone'));
            // UTC±00:00 is UTC

            $pattern = '/^(UTC)[+-][0-9]{2}:[0-9]{2}$/';
            if (!empty($timezone) && preg_match($pattern, $timezone) == true) {
                return str_replace('UTC', '', $timezone);
            }
        }
        if (!in_array($timezone, \DateTimeZone::listIdentifiers())) {
            $timezone = config('app.timezone');
        }

        return $timezone;
    }

    /**
     * convert time to database time.
     *
     * @param mixed   $time        Time
     * @param boolean $fromRequest From Request
     *
     * @return Carbon|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function toDatabaseTime(mixed $time, bool $fromRequest = false): ?Carbon
    {
        if (empty($time)) {
            return null;
        }
        if (is_string($time)) {
            $timezone = $fromRequest ? self::currentTimezone() : config('app.timezone');
            $time = self::stringToTime($time, $timezone);
        }
        return $time->copy()->setTimezone(config('app.timezone'));
    }

    /**
     * String To Time
     *
     * @param mixed  $str       String
     * @param string $timezone Timezone
     *
     * @return Carbon
     */
    public static function stringToTime(mixed $str, string $timezone = ''): Carbon
    {
        if (empty($timezone)) {
            $timezone = config('app.timezone');
        }
        $str = replaceAll('/', '-', $str);
        $str = replaceAll('.', '-', $str);

        return Carbon::parse($str, $timezone);
    }

    /**
     * To Database Date
     *
     * @param mixed $date Date
     *
     * @return Carbon|null
     */
    public static function toDatabaseDate(mixed $date): ?Carbon
    {
        if (empty($date)) {
            return null;
        }
        if (is_string($date)) {
            $date = self::stringToTime($date);
        }
        return $date->copy();
    }

    /**
     * Convert
     *
     * @param mixed $time       Time
     * @param array $converters Converters
     *
     * @return mixed
     */
    public static function convert(mixed $time, array $converters = []): mixed
    {
        if (empty($time)) {
            return null;
        }

        if (is_string($converters)) {
            $converters = explode(',', $converters);
        }

        foreach ($converters as $converter) {
            $converter = trim($converter);
            if (method_exists($time, $converter)) {
                $time->$converter();
            }
        }

        return $time;
    }

    /**
     * Show Date
     *
     * @param mixed $date             Date
     * @param bool $includeHourMinute Include Hour Minute
     * @param bool $includeSecond     Include Second
     *
     * @return string
     */
    public static function showDate(mixed $date, mixed $includeHourMinute = true, bool $includeSecond = true): string
    {
        if (empty($date)) {
            return '';
        }

        if (is_string($date)) {
            $date = self::stringToTime($date);
        }

        $date = $date->copy()->setTimezone(self::currentTimezone());
        $format = 'Y-m-d';

        if ($includeHourMinute) {
            $format .= ' H:i';
            if ($includeSecond) {
                $format .= ':s';
            }
        }

        return $date->format($format);
    }

    /**
     * Convert Date To Resource
     *
     * @param mixed      $date      Date
     * @param mixed|null $format    Format
     * @param string     $timezone  Timezone
     *
     * @return string|null
     */
    public static function convertDateToResource(mixed $date, mixed $format = null, string $timezone = 'Asia/Tokyo'): ?string
    {
        if (empty($date)) {
            return null;
        }

        if (is_string($date)) {
            $date = self::stringToTime($date);
        }

        if ($timezone != 'Asia/Tokyo') {
            $dateStr = $date->copy()->format('Y-m-d H:i:s');
            $date = self::stringToTime($dateStr, $timezone);
            $date->setTimezone('Asia/Tokyo');
        }

        if (!empty($format)) {
            return $date->copy()->format($format);
        }

        return $date->copy()->toAtomString();
    }
}
