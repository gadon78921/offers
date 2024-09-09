<?php

declare(strict_types=1);

namespace App\Esb\In\RetailSupplierPrices\Commands;

final class ImportSupplierPrice
{
    public function __construct(
        public readonly int $supplierId,
        public readonly string $filePath,
    ) {}
}
