<?php

declare(strict_types=1);

namespace App\OffersBuilding\Domain\Entity;

final class TradePoint
{
    /**
     * @param array{int} $firmIds
     * @param array{int} $supplierIds
     */
    public function __construct(
        public readonly int $id,
        public readonly string $kladrId,
        public readonly bool $deliveryAvailable,
        public readonly array $firmIds,
        public readonly array $supplierIds,
    ) {}
}
