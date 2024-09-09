<?php

declare(strict_types=1);

namespace App\OffersBuilding\Commands;

final class BuildOffersByAssortmentUnitIds implements AsyncCommand
{
    public function __construct(
        /** @var array<int> $assortmentUnitIds */
        public readonly array $assortmentUnitIds,
    ) {}
}
