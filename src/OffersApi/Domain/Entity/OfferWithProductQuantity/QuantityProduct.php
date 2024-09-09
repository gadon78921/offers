<?php

declare(strict_types=1);

namespace App\OffersApi\Domain\Entity\OfferWithProductQuantity;

final class QuantityProduct
{
    public function __construct(
        public int $retailProductId,
        public int $quantity,
        public int $quantityUnpacked,
    ) {}
}
