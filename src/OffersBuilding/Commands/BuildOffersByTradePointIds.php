<?php

declare(strict_types=1);

namespace App\OffersBuilding\Commands;

final class BuildOffersByTradePointIds implements AsyncCommand
{
    public readonly string $msgHash;

    public function __construct(
        /** @var array<int> $tradePointIds */
        public readonly array $tradePointIds,
    ) {
        $this->msgHash = hash('sha256', json_encode($tradePointIds, JSON_THROW_ON_ERROR));
    }
}
