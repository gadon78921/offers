<?php

declare(strict_types=1);

namespace App\Esb\In\RetailSupplierPrices\Messages;

final class SupplierPricesData
{
    public function __construct(
        public readonly string $protocol,
        public readonly string $host,
        public readonly ?int $port,
        public readonly string $path,
        public readonly string $file,
    ) {}

    public function fullFileName(): string
    {
        return $this->path . $this->file;
    }
}
