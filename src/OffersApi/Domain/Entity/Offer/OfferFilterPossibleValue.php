<?php

declare(strict_types=1);

namespace App\OffersApi\Domain\Entity\Offer;

final class OfferFilterPossibleValue
{
    public function __construct(
        public readonly string $key,
        public readonly string $value,
        public readonly int $count,
    ) {}

    public function isUndefinedValue(): bool
    {
        return str_starts_with($this->value, 'не указан');
    }
}
