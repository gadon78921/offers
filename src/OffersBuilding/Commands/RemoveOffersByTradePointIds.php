<?php

declare(strict_types=1);

namespace App\OffersBuilding\Commands;

final class RemoveOffersByTradePointIds implements AsyncCommand
{
    public function __construct(
        /** @var array<int> $tradePointIds */
        public readonly array $tradePointIds,
    ) {}
}
