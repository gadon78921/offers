<?php

declare(strict_types=1);

namespace App\OffersApi\Domain\Entity\Offer;

use Doctrine\Common\Collections\ArrayCollection;

final class OfferFilter
{
    /**
     * @param ArrayCollection<int, OfferFilterPossibleValue> $possibleValues
     * @param list<string>                                   $values
     */
    public function __construct(
        public readonly string $type,
        public readonly string $itemType,
        public readonly string $name,
        public readonly OfferFilterView $view,
        public readonly ArrayCollection $possibleValues,
        public readonly bool $isAvailableForFastAccess,
        public readonly array $values,
    ) {}

    public function isAvailableForFastAccess(): bool
    {
        return $this->isAvailableForFastAccess && $this->possibleValues->count() > 1 && $this->possibleValues->count() <= 20;
    }

    public function hasUndefinedValue(): bool
    {
        return $this->possibleValues->exists(fn($key, OfferFilterPossibleValue $possibleValue) => $possibleValue->isUndefinedValue());
    }
}
