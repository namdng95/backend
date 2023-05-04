<?php

namespace App\Core\Contracts;

use Illuminate\Support\Collection;

interface HasCriteriaInterface
{
    /**
     * Push new criteria to the stack
     *
     * @param mixed $criteria Criteria
     *
     * @return self
     */
    public function pushCriteria(mixed $criteria): HasCriteriaInterface;

    /**
     * Pop criteria to the stack
     *
     * @param mixed $criteria Criteria
     *
     * @return self
     */
    public function popCriteria(mixed $criteria): HasCriteriaInterface;

    /**
     * Get list of criteria
     *
     * @return Collection
     */
    public function getCriteria(): Collection;

    /**
     * Reset the list of criteria
     *
     * @return self
     */
    public function resetCriteria(): HasCriteriaInterface;

    /**
     * Apply the criteria to handler
     *
     * @return self
     */
    public function applyCriteria(): HasCriteriaInterface;
}
