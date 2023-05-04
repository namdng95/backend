<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('replaceAll')) {
    function replaceAll($find, $replace, $str)
    {
        while (str_contains($str, $find)) {
            $str = str_replace($find, $replace, $str);
        }

        return $str;
    }
}

if (!function_exists('filterToArray')) {
    function filterToArray($filter = '')
    {
        if (is_string($filter)) {
            $filter = explode(',', $filter);
        }

        if (!is_array($filter)) {
            $filter = [$filter];
        }

        return $filter;
    }
}

if (!function_exists('authUser')) {
    function authUser($default = null)
    {
        return \Auth::check() ? \Auth::user() : $default;
    }
}

if (!function_exists('authId')) {
    function authId($default = null)
    {
        return \Auth::check() ? \Auth::id() : $default;
    }
}

if (!function_exists('authCompanyId')) {
    function authCompanyId($default = null)
    {
        return \Auth::check() ? \Auth::user()->company_id : $default;
    }
}

if (!function_exists('authCompany')) {
    function authCompany($default = null)
    {
        return \Auth::check() ? \Auth::user()->company : $default;
    }
}

if (!function_exists('authCompanyCode')) {
    function authCompanyCode($default = null)
    {
        return \Auth::check() && \Auth::user()->company
            ? \Auth::user()->company->code
            : $default;
    }
}

if (!function_exists('isApi')) {
    function isApi(): bool
    {
        return request()->is('api/*');
    }
}

if (!function_exists('isAjax')) {
    function isAjax(): bool
    {
        return request()->is('ajax/*');
    }
}

if (!function_exists('trimSpaceJapanese')) {
    function trimSpaceJapanese($data = [], $excludes = []): mixed
    {
        if (empty($data)) {
            return $data;
        }

        if (is_array($data) || is_string($data)) {
            return preg_replace("/(^\s+)|(\s+$)/us", "", $data);
        }

        foreach ($data as $k => $item) {
            if (in_array($k, $excludes)) {
                continue;
            }

            if (is_array($data)) {
                $data[$k] = trimSpaceJapanese($item);
                continue;
            }

            $data[$k] = preg_replace("/(^\s+)|(\s+$)/us", "", $item);
        }

        return $data;
    }
}

if (!function_exists('randomString')) {
    function randomString($len = 8): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randString = '';

        for ($i = 0; $i < $len; $i++) {
            $randString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randString;
    }
}

if (!function_exists('generateMongoId')) {
    function generateMongoId(): string
    {
        // Building binary data.
        $bin = sprintf(
            "%s%s%s%s",
            pack('N', milliseconds()),
            substr(md5(php_uname('n')), 0, 3),
            pack('n', getmypid() * rand(1, 100000)),
            substr(pack('N', uniqid(rand())), 1, 3)
        );

        // Convert binary to hex.
        $mongoId = '';

        for ($i = 0; $i < 12; $i++) {
            $mongoId .= sprintf("%02x", ord($bin[$i]));
        }

        return $mongoId;
    }
}

if (!function_exists('milliseconds')) {
    function milliseconds()
    {
        $mt = explode(' ', microtime());

        return ((int)$mt[1]) * 1000 + ((int)round($mt[0] * 10000000));
    }
}

if (!function_exists('escapeStringUuid')) {
    function escapeStringUuid(string $str): string
    {
        $string = $str;
        $emotions = config('emotions', []);

        foreach ($emotions as $e) {
            $string = replaceAll($e, '', $string);
        }

        if ($string == $str) { // no emotion
            return escapeString($str);
        }

        return $string . 'No-Include';
    }
}

if (!function_exists('escapeString')) {
    function escapeString(string $string): string
    {
        $string = str_replace('\\', '\\\\', mb_strtolower($string));

        return addcslashes($string, '%_');
    }
}

if (!function_exists('convertToArray')) {
    function convertToArray($string = null, $key = ',', $trim = true, bool $strToLower = false, $keepEmpty = false): array
    {
        $string = $string ?? [];
        $parsers = [];

        if (is_array($string)) {
            $parsers = $string;
        }

        if (is_string($string)) {
            $parsers = explode($key, $string);
        }

        if (!is_array($string) && !is_string($string)) {
            $parsers = [$string];
        }

        foreach ($parsers as $i => &$parser) {
            if (is_string($parser)) {
                if (!$keepEmpty && trim($parser) == '') {
                    unset($parsers[$i]);
                    continue;
                }

                if ($trim) {
                    $parser = trim($parser);
                }

                if ($strToLower) {
                    $parser = mb_strtolower($parser);
                }
            }
        }

        return array_values($parsers);
    }
}

if (!function_exists('arrayOnly')) {
    function arrayOnly($array, $keys = []): array
    {
        if (empty($array) || empty($keys)) {
            return [];
        }

        $result = [];
        if (is_string($keys)) {
            $keys = convertToArray($keys);
        }

        foreach ($keys as $key) {
            if (isset($array[$key])) {
                $result[$key] = $array[$key];
            }
        }

        return $result;
    }
}

if (!function_exists('calculateProductTaxAutomation')) {
    function calculateProductTaxAutomation($taxId, $price, $residualsCalculation): string
    {
        $rate = \App\Enums\Common\ProductTax::get($taxId);
        $tax = strval(calculateHugeNumber($price, ($rate['value'] / 100), '*'));

        return roundHugeNumber($tax, $residualsCalculation);
    }
}

if (!function_exists('roundHugeNumber')) {
    function roundHugeNumber($numberStr, $round = 0): string
    {
        $numberStr = trim(strval($numberStr));

        if (!str_contains($numberStr, '.')) {
            return $numberStr;
        }

        $parser = explode('.', $numberStr);
        $so = $parser[0];
        $du = $parser[1];

        if ($so[0] != '-') { // so duong
            if ($round == \App\Enums\Common\CalculationRound::CEIL) {
                $so = calculateHugeNumber($so, 1);
            }
            if ($round == \App\Enums\Common\CalculationRound::ROUND) {
                if (intval($du[0]) >= 5) {
                    $so = calculateHugeNumber($so, 1);
                }
            }
        }

        if ($so[0] == '-') { // so am
            if ($round == \App\Enums\Common\CalculationRound::FLOOR) {
                $so = calculateHugeNumber($so, 1, '-');
            }

            if ($round == \App\Enums\Common\CalculationRound::ROUND) {
                if (intval($du[0]) >= 5) {
                    $so = calculateHugeNumber($so, 1, '-');
                }
            }
        }

        if ($so == '' || $so == '-0') {
            $so = '0';
        }

        return $so;
    }
}

if (!function_exists('calculateHugeNumber')) {
    function calculateHugeNumber($number1, $number2, $comma = '+'): string
    {
        $number1 = replaceAll(',', '', strval($number1));
        $number2 = replaceAll(',', '', strval($number2));

        if ($comma == ':') {
            $comma = '/';
        }

        $formula = $number1 . $comma . $number2;
        $result = trim(shell_exec('echo "scale=2;' . $formula . '"|bc'));
        $prefix = str_starts_with($result, '-') ? '-' : '';
        $result = ltrim($result, '-0');

        if (str_contains($result, '.')) {
            $result = rtrim($result, '0');
            $result = rtrim($result, '.');
        }

        if (str_starts_with($result, '.')) {
            $result = '0' . $result;
        }

        if ($result === '' || $result === '0') {
            return '0';
        }

        return $prefix . $result;
    }
}

if (!function_exists('showTextOnPdf')) {
    function showTextOnPdf($text): string
    {
        $array = mb_str_split($text);
        foreach ($array as &$item) {
            if ($item == ' ') {
                $item = '&nbsp;';
            }
        }

        return '<span class="nobr">' . implode('</span><span class="nobr">', $array) . '</span>';
    }
}

if (!function_exists('showNumberOnPdf')) {
    function showNumberOnPdf($num, $html = true): string
    {
        $num = strval($num);
        $prefix = '';

        if (str_starts_with($num, '-')) {
            $prefix = '-';
            $num = substr($num, 1);
        }

        $afterDot = '';
        if (str_contains($num, '.')) {
            $parser = explode('.', $num, 2);
            if (trim($parser[1] ?? '') !== '') {
                $afterDot = '.' . trim($parser[1] ?? '');
            }
            $num = trim($parser[0] ?? '');
        }

        $parser = [];
        if (str_contains($num, ',')) {
            $parser = explode(',', $num);
        }

        if (!str_contains($num, ',')) {
            $tmp = '';
            for ($i = strlen($num) - 1; $i >= 0; $i--) {
                $tmp = $num[$i] . $tmp;
                if (strlen($tmp) >= 3) {
                    array_unshift($parser, $tmp);
                    $tmp = '';
                }
            }

            if ($tmp !== '') {
                array_unshift($parser, $tmp);
            }
        }

        if (!$html) {
            return $prefix . '' . implode(',', $parser);
        }

        $result = [];

        foreach ($parser as $i => $par) {
            if ($i == 0) {
                $par = $prefix . $par;
            }

            if ($i < count($parser) - 1) {
                $par .= ',';
            }
            $result[] = '<span class="nobr">' . $par . '</span>';
        }

        return implode('', $result) . ($afterDot === '' ? '' : '<span class="nobr">' . $afterDot . '</span>');
    }
}

if (!function_exists('imageToBase64')) {
    function imageToBase64($path, $disk = null): string
    {
        $image = empty($disk) ? \Storage::get($path) : \Storage::disk($disk)->get($path);
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        return 'data:image/' . $ext . ';base64,' . base64_encode($image);
    }
}

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle): bool
    {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

if (!function_exists('addString')) {
    function addString(&$text, $add = ''): void
    {
        if (!empty($text)) {
            $text .= ', ';
        }

        $text .= $add;
        $text = trim($text);
    }
}

if (!function_exists('getIp')) {
    function getIp($default = true)
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $_SERVER['REMOTE_ADDR'] ?? ($default ? '0.0.0.0' : null);
    }
}

if (!function_exists('array_insert')) {
    function array_insert($array, $position, $insert): bool|array
    {
        $size = count($array);

        if (!is_int($position) || $position < 0 || $position > $size) {
            return false;
        }

        $temp = array_slice($array, 0, $position);
        $temp[] = $insert;

	    return array_merge($temp, array_slice($array, $position, $size));
    }
}

if (!function_exists('dateImportValid')) {
    function dateImportValid($date): bool|int
    {
        $regex = "/^\d{4}\/(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])$/";

        return preg_match($regex, $date);
    }
}

if (!function_exists('invoiceCodeImportValid')) {
    function invoiceCodeImportValid($code): bool|int
    {
        $regex = "/^[a-zA-Z0-9!@#$%^&*()_+\-=~`\[\]{};'\":|,.<>\/\\\?]*$/";

        return preg_match($regex, $code);
    }
}

if (!function_exists('halfWidthValid')) {
    function halfWidthValid($code): bool|int
    {
        $regex = '/^[A-Za-z0-9]+$/';

        return preg_match($regex, $code);
    }
}

//if (!function_exists('incrementInfo')) {
//    function incrementInfo($employee = null, $field = 'count_notifications')
//    {
//        if (is_null($employee)) {
//            $employee = authUser();
//        }
//        if (!$employee) {
//            return;
//        }
//        if ($employee->info) {
//            return $employee->info->increment($field);
//        }
//        $employee->info()->create([
//            $field => 1
//        ]);
//    }
//}
//
//if (!function_exists('decrementInfo')) {
//    function decrementInfo($employee = null, $field = 'count_notifications')
//    {
//        if (is_null($employee)) {
//            $employee = authUser();
//        }
//        if (!$employee) {
//            return;
//        }
//        if ($employee->info) {
//            return $employee->info->decrement($field);
//        }
//        $employee->info()->create([
//            $field => 0
//        ]);
//    }
//}

//if (!function_exists('updateInfo')) {
//    function updateInfo($employee = null, $data = [], $defaultAuth = true)
//    {
//        if (is_null($employee) && $defaultAuth) {
//            $employee = authUser();
//        }
//        if (!$employee || empty($data)) {
//            return;
//        }
//        if ($employee->info) {
//            return $employee->info->update($data);
//        }
//        $employee->info()->create($data);
//    }
//}

if (!function_exists('base64url_encode')) {
    function base64url_encode($str): array|string
    {
        $base64 = base64_encode($str);

        return str_replace(['+', '/'], ['-', '_'], $base64);
    }
}

if (!function_exists('base64url_decode')) {
    function base64url_decode($str): bool|string
    {
        $replace = str_replace(['-', '_'], ['+', '/'], $str);

        return base64_decode($replace);
    }
}

if (!function_exists('addMapToArray')) {
    function addMapToArray(&$array, $key, $value): void
    {
        if (is_array($key)) {
            foreach ($key as $subKey) {
                addMapToArray($array, $subKey, $value);
            }

            return;
        }

        if (is_numeric($value)) {
            $array[$key] = $value;
        }

        if (is_array($value)) {
            $explode = explode('_', $key);
            array_pop($explode);
            $key = implode('_', $explode);

            if (count($value) == 2) {
                $array[$key . '_viewable'] = $value[0];
                $array[$key . '_editable'] = $value[1];
            }

            if (count($value) == 4) {
                $array[$key . '_viewable'] = $value[0];
                $array[$key . '_creatable'] = $value[1];
                $array[$key . '_editable'] = $value[2];
                $array[$key . '_deletable'] = $value[3];
            }
        }
    }
}

if (!function_exists('escapeFileName')) {
    function escapeFileName($fileName)
    {
        return replaceAll('/', '_', replaceAll('\\', '_', $fileName));
    }
}

if (!function_exists('hasSearch')) {
    function hasSearch($params, $key = ''): bool
    {
        $value = '';

        if (is_numeric($params)) {
            $value = strval($params);
        }

        if (is_string($params)) {
            $value = $params;
        }

        if (is_array($params)) {
            $value = $params[$key] ?? '';
        }

        return trim($value) !== '';
    }
}

if (!function_exists('logSlack')) {
    function logSlack($message): void
    {
        if (empty(config('logging.channels.slack.url'))) {
            return;
        }

        Log::channel('slack')->debug(date('Y-m-d H:i:s') . ' - debugging - ' . authId(0));
        Log::channel('slack')->debug($message);
    }
}

if (!function_exists('hasNoItemOption')) {
    function hasNoItemOption($checkAll = false, $ids = []): bool
    {
        if ($ids == []) {
            return false;
        }

        if (!$checkAll) {
            return in_array(0, $ids);
        }

        return !in_array(0, $ids);
    }
}

if (!function_exists('getDpi')) {
    function getDpi($file): array
    {
        $a = fopen($file, 'r');
        $string = fread($a, 20);
        fclose($a);

        $data = bin2hex(substr($string, 14, 4));
        $x = substr($data, 0, 4);
        $y = substr($data, 4, 4);

        return [hexdec($x),hexdec($y)];
    }
}


if (!function_exists('decrypt_string')) {
    function decrypt_string($secretKey, $column): string
    {
        return "AES_DECRYPT(from_base64($column), '$secretKey')";
    }
}

if (!function_exists('onlyTrimSpace')) {
    function onlyTrimSpace($text)
    {
        if (empty($text)) {
            return $text;
        }

        return join("\n", array_map("trim", explode("\n", $text)));
    }
}
