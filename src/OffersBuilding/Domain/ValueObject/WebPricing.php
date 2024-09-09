<?php

declare(strict_types=1);

namespace App\OffersBuilding\Domain\ValueObject;

final class WebPricing
{
    public function __construct(
        public readonly float $priceWithoutDiscount,
        public readonly float $priceForPreorder,
        public readonly float $priceForWaiting,
        public readonly int $discountForPreorder,
        public readonly int $discountForWaiting,
        public readonly bool $isFixedDiscount,
    ) {}
}
