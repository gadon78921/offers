<?php

declare(strict_types=1);

namespace App\OffersApi\Domain\Entity\Offer;

final class OfferFilterView
{
    public function __construct(
        public readonly string $title,
        public readonly string $viewType,
        public readonly string $gender,
    ) {}
}
