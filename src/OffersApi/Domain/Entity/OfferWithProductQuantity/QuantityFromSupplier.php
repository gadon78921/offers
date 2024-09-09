<?php

declare(strict_types=1);

namespace App\OffersApi\Domain\Entity\OfferWithProductQuantity;

use Doctrine\Common\Collections\ArrayCollection;

final class QuantityFromSupplier
{
    /**
     * @param ArrayCollection<int, QuantityProduct> $quantity
     */
    public function __construct(
        public int $supplierId,
        public ArrayCollection $quantity,
    ) {}

    public function totalQuantityFromSupplier(): int
    {
        $totalQuantity = 0;

        foreach ($this->quantity as $quantityProduct) {
            $totalQuantity += $quantityProduct->quantity;
        }

        return $totalQuantity;
    }
}
