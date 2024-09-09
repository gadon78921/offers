<?php

declare(strict_types=1);

namespace App\OffersBuilding\Commands;

final class BuildOffersByKladrId implements AsyncCommand
{
    public function __construct(
        public readonly string $kladrId,
    ) {}
}
