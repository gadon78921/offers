<?php

declare(strict_types=1);

namespace App\OffersApi\Domain\Entity\Offer;

use Doctrine\Common\Collections\ArrayCollection;

final class Offer
{
    /**
     * @param ArrayCollection<int, OfferReadyTime> $readyTimes
     */
    public function __construct(
        public int $assortmentUnitId,
        public float $price,
        public float $priceForPreorder,
        public float $priceForWaiting,
        public int $discountForPreorder,
        public int $discountForWaiting,
        public float $wholesalePrice,
        public ArrayCollection $readyTimes,
        public ?OfferDescription $offerDescription = null,
    ) {}
}
