<?php

declare(strict_types=1);

namespace App\OffersBuilding\Repository\WebPricing;

use App\OffersBuilding\Domain\ValueObject\WebPricing;

final class WebPricingHydrator
{
    /** @param array{'priceWithoutDiscount': float, 'priceForPreorder': float, 'priceForWaiting': float, 'discountForPreorder': int, 'discountForWaiting': int, 'isFixedDiscount': bool} $price */
    public function hydrateWebPricing(array $price): WebPricing
    {
        return new WebPricing(
            (float) $price['priceWithoutDiscount'],
            (float) $price['priceForPreorder'],
            (float) $price['priceForWaiting'],
            (int) $price['discountForPreorder'],
            (int) $price['discountForWaiting'],
            (bool) $price['isFixedDiscount']
        );
    }
}
