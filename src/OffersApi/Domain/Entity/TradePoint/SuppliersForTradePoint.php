<?php

declare(strict_types=1);

namespace App\OffersApi\Domain\Entity\TradePoint;

final class SuppliersForTradePoint
{
    public function __construct(
        public readonly int $tradePointId,
        /** @var array<int> $supplierIds */
        public readonly array $supplierIds,
    ) {}
}
