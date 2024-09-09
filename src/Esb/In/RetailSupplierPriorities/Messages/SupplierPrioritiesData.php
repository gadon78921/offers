<?php

declare(strict_types=1);

namespace App\Esb\In\RetailSupplierPriorities\Messages;

final class SupplierPrioritiesData
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
