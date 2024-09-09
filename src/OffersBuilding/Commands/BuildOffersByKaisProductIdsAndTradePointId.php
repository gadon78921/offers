<?php

declare(strict_types=1);

namespace App\OffersBuilding\Commands;

final class BuildOffersByKaisProductIdsAndTradePointId implements AsyncCommand
{
    public function __construct(
        /** @var array<int> $kaisProductIds */
        public readonly array $kaisProductIds,
        public readonly int $tradePointId,
    ) {}
}
