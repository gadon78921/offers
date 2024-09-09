<?php

declare(strict_types=1);

namespace App\OffersBuilding\Domain\Entity;

final class ProductInTradePoint
{
    public function __construct(
        public readonly int $kaisProductId,
        public readonly int $assortmentUnitId,
        public readonly string $productName,
        public readonly TradePoint $tradePoint,
        public readonly float $price,
        public readonly float $wholesalePrice,
        public readonly int $quantity,
        public readonly int $quantityUnpacked,
    ) {}
}
