<?php

namespace App\Core\Traits;

use Illuminate\Support\Collection;
use App\Core\Contracts\CriteriaInterface;
use App\Core\Exceptions\RepositoryException;
use Throwable;

trait HasCriteria
{
    /**
     * @var boolean
     */
    protected bool $skipCriteria = false;

    /**
     * @var Collection
     */
    protected Collection $criteria;

    /**
     * Push new criteria to the stack
     *
     * @param mixed $criteria Criteria
     *
     * @return HasCriteria
     * @throws Throwable
     */
    public function pushCriteria(mixed $criteria): static
    {
        if (is_string($criteria)) {
            $criteria = new $criteria;
        }

        if (! $criteria instanceof CriteriaInterface) {
            throw RepositoryException::invalidMethod();
        }

        $this->criteria->push($criteria);

        return $this;
    }

    /**
     * Pop criteria to the stack
     *
     * @param mixed $criteria
     * @return self
     */
    public function popCriteria(mixed $criteria): static
    {
        $this->criteria = $this->criteria->reject(function ($item) use ($criteria) {
            if (is_object($item) && is_string($criteria)) {
                return get_class($item) === $criteria;
            }

            if (is_string($item) && is_object($criteria)) {
                return $item === get_class($criteria);
            }

            return get_class($item) === get_class($criteria);
        });

        return $this;
    }

    /**
     * Get list of criteria
     *
     * @return Collection
     */
    public function getCriteria(): Collection
    {
        return $this->criteria;
    }

    /**
     * Reset the list of criteria
     *
     * @return self
     */
    public function resetCriteria(): static
    {
        $this->criteria = new Collection();

        return $this;
    }

    /**
     * Apply the criteria to handler
     *
     * @return self
     */
    public function applyCriteria(): static
    {
        if ($this->skipCriteria) {
            return $this;
        }

        $cr = $this->getCriteria();

        foreach ($cr as $c) {
            $this->model = $c->apply($this->model, $this);
        }

        return $this;
    }
}
