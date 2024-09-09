<?php

declare(strict_types=1);

namespace App\OffersApi\Domain\Entity\Offer;

use Doctrine\Common\Collections\ArrayCollection;

final class OfferFilterCollection
{
    /**
     * @var ArrayCollection<int, OfferFilter>
     */
    public ArrayCollection $values;

    public function __construct()
    {
        $this->values = new ArrayCollection();
    }

    /**
     * @return ArrayCollection<int, OfferFilter>
     */
    public function getMoreThanOnePossibleValues(): ArrayCollection
    {
        return $this->values->filter(fn(OfferFilter $filter) => $filter->possibleValues->count() > 1);
    }

    /**
     * @return ArrayCollection<int, OfferFilter>
     */
    public function previousFastAccessFilter(): ArrayCollection
    {
        $values = $this->getMoreThanOnePossibleValues();

        return $values->filter(fn(OfferFilter $filter) => $filter->isAvailableForFastAccess() && !empty($filter->values));
    }

    public function getNextFastAccessFilter(): ?OfferFilter
    {
        $allFastAccessFilters                                                                 = $this->values->filter(fn(OfferFilter $filter) => $filter->isAvailableForFastAccess());
        [$fastAccessFiltersWithNullPossibleValue, $fastAccessFiltersWithoutNullPossibleValue] = $allFastAccessFilters->partition(function ($key, OfferFilter $filter) {
            return $filter->hasUndefinedValue();
        });

        $nextFastAccessFilter = $fastAccessFiltersWithoutNullPossibleValue->first();
        $nextFastAccessFilter = false === $nextFastAccessFilter ? $fastAccessFiltersWithNullPossibleValue->first() : $nextFastAccessFilter;

        return false === $nextFastAccessFilter ? null : $nextFastAccessFilter;
    }
}
