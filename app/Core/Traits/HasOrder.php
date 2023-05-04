<?php

namespace App\Core\Traits;

trait HasOrder
{
    /**
     * Get Order-able Fields
     *
     * @return array
     */
    public function getOrderableFields(): array
    {
        return [];
    }
}
