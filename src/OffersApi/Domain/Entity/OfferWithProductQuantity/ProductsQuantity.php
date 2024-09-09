<?php

declare(strict_types=1);

namespace App\OffersApi\Domain\Entity\OfferWithProductQuantity;

use Doctrine\Common\Collections\ArrayCollection;

final class ProductsQuantity
{
    /**
     * @param ArrayCollection<int, QuantityProduct>      $quantityInTradePoint
     * @param ArrayCollection<int, QuantityFromSupplier> $quantityFromSuppliers
     */
    public function __construct(
        public int $tradePointId,
        public ArrayCollection $quantityInTradePoint,
        public ArrayCollection $quantityFromSuppliers,
    ) {}
}
