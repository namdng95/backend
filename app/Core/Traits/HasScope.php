<?php

namespace App\Core\Traits;

use Closure;

trait HasScope
{
    /**
     * @var Closure|null
     */
    protected ?Closure $scopeQuery = null;

    /**
     * Add custom scope to handler
     *
     * @param Closure $scope Scope
     * @return self
     */
    public function scopeQuery(Closure $scope): static
    {
        $this->scopeQuery = $scope;

        return $this;
    }

    /**
     * Reset handler scope
     *
     * @return self
     */
    public function resetScope(): static
    {
        $this->scopeQuery = null;

        return $this;
    }

    /**
     * Apply scope in current Query
     *
     * @return $this
     */
    public function applyScope(): static
    {
        if (isset($this->scopeQuery) && is_callable($this->scopeQuery)) {
            $callback = $this->scopeQuery;
            $this->model = $callback($this->model);
        }

        return $this;
    }
}
