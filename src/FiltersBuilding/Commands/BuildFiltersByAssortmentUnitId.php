<?php

declare(strict_types=1);

namespace App\FiltersBuilding\Commands;

final class BuildFiltersByAssortmentUnitId implements AsyncCommand
{
    public function __construct(
        public readonly int $assortmentUnitId,
    ) {}
}
