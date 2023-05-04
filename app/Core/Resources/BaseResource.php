<?php

namespace App\Core\Resources;

use App\Helpers\DateTimeHelper;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class BaseResource extends JsonResource
{
    /**
     * @var
     */
    public $resource;

    /**
     * Result
     *
     * @param mixed $columns Columns
     *
     * @return array
     */
    public function result(mixed $columns): array
    {
        $timezone = 'UTC';

        $dates = [
            'birthday', 'issued_on', 'date_of_establishment', 'joined_on', 'retirement_date',
            'ts_password_use_start', 'ts_password_use_end', 'start_year_date', 'send_money_date', 'disable_date',
            'deadline_date', 'send_mail_date', 'send_office_date','deprecated_date',
            'start_date', 'end_date'
        ];
        $datetime = [
            'banned_at', 'last_login_at', 'last_activity_at', 'verified_email_at', 'verified_phone_at', 'verified_at',
            'deleted_at', 'updated_at', 'created_at', 'download_at', 'send_at', 'requested_at', 'start_at', 'end_at'
        ];

        $result = [];

        foreach ($columns as $column) {
            $result[$column] = $this->resource->$column;

            $attribute = $column.'_text';
            if (!empty($this->resource->$attribute)) {
                $result[$attribute] = $this->resource->$attribute;
            }

            $attribute = 'full_'.$column;
            if (!empty($this->resource->$attribute)) {
                $result[$attribute] = $this->resource->$attribute;
            }

            $attribute = $column.'_count';
            if (isset($this->resource->$attribute)) {
                $result[$attribute] = $this->resource->$attribute;
            }

            if (in_array($column, $dates)) {
                $result[$column] = DateTimeHelper::convertDateToResource($this->resource->$column, 'Y/m/d');
            }

            if (in_array($column, $datetime)) {
                $result[$column] = DateTimeHelper::convertDateToResource($this->resource->$column, null, $timezone);
            }
        }

        return $result;
    }
}
