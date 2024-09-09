<?php

declare(strict_types=1);

namespace App\Esb\In\RetailOrderCityPrices\Commands;

final class ImportOrderPrice
{
    public function __construct(
        public readonly string $kladrId,
        public readonly string $filePath,
    ) {}
}
