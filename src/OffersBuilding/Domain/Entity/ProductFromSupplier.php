<?php

declare(strict_types=1);

namespace App\OffersBuilding\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;

final class ProductFromSupplier
{
    public function __construct(
        public readonly int $kaisProductId,
        public readonly int $assortmentUnitId,
        public readonly string $productName,
        public readonly int $supplierId,
        public readonly float $price,
        public readonly int $quantity,
        /** @var ArrayCollection<int, TradePoint> */
        public ArrayCollection $tradePoints,
    ) {}
}
