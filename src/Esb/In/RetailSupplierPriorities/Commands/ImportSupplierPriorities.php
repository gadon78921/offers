<?php

declare(strict_types=1);

namespace App\Esb\In\RetailSupplierPriorities\Commands;

final class ImportSupplierPriorities
{
    public function __construct(
        public readonly int $regionId,
        public readonly string $filePath,
    ) {}
}
