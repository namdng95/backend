<?php

namespace App\Core\Resources;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class BaseCollection extends ResourceCollection
{
    /**
     * @var string|null
     */
    protected ?string $resourceName = null;

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray($request = null): array
    {
        if ($this->resource instanceof LengthAwarePaginator) {
            if ($this->resource->total() > $this->resource->perPage()) {
                return [
                    'current_page' => $this->resource->currentPage(),
                    'data' => $this->getResourceClass()::collection($this->resource->getCollection()),
                    'last_page' => $this->resource->lastPage(),
                    'limit' => $this->resource->perPage(),
                    'total' => $this->resource->total(),
                ];
            }
        }

        return [
            'current_page' => 1,
            'data' => $this->getResourceClass()::collection($this->resource),
            'last_page' => 1,
            'limit' => intval(request('limit', $this->resource->count())),
            'total' => $this->resource->count(),
        ];
    }

    /**
     * Get resource class name from collection
     *
     * @return string
     */
    protected function getResourceClass(): string
    {
        return $this->resourceName ?? Str::replaceLast('Collection', 'Resource', get_class($this));
    }
}
