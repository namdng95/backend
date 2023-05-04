<?php

namespace App\Core\Contracts;

use Closure;

interface HasScopeInterface
{
    /**
     * Add custom scope to handler
     *
     * @param Closure $scope Scope
     *
     * @return self
     */
    public function scopeQuery(Closure $scope): HasScopeInterface;

    /**
     * Reset handler scope
     *
     * @return self
     */
    public function resetScope(): HasScopeInterface;
}
