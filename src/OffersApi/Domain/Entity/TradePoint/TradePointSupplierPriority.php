<?php

declare(strict_types=1);

namespace App\OffersApi\Domain\Entity\TradePoint;

final class TradePointSupplierPriority
{
    public function __construct(
        public int $tradePointId,
        /** @var array<int> $supplierIds */
        public array $supplierIds,
    ) {}
}
