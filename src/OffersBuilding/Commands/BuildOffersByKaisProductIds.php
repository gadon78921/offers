<?php

declare(strict_types=1);

namespace App\OffersBuilding\Commands;

final class BuildOffersByKaisProductIds implements AsyncCommand
{
    public function __construct(
        /** @var array<int> $kaisProductIds */
        public readonly array $kaisProductIds,
    ) {}
}
