<?php

declare(strict_types=1);

namespace App\OffersApi\Domain\Entity\OfferWithProductQuantity;

use Doctrine\Common\Collections\ArrayCollection;

final class OfferWithProductsQuantity
{
    /**
     * @param ArrayCollection<int, ProductsQuantity> $productsQuantity
     */
    public function __construct(
        public int $assortmentUnitId,
        public ArrayCollection $productsQuantity,
    ) {}
}
