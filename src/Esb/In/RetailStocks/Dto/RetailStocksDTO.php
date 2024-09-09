<?php

declare(strict_types=1);

namespace App\Esb\In\RetailStocks\Dto;

final class RetailStocksDTO
{
    public function __construct(
        public readonly int $kaisProductId,
        public readonly string $firmSubdivisionId,
        public readonly int $storeId,
        public readonly int $freeQty,
        public readonly int $dividedFreeQty,
        public readonly float $retailPriceWithTax,
        public readonly float $avgIncomePriceWithTax,
    ) {}

    /** @return array{'kaisProductId': int, 'firmSubdivisionId': string, 'storeId': int, 'freeQty': int, 'dividedFreeQty': int, 'retailPriceWithTax': float, 'avgIncomePriceWithTax': float} */
    public function toArray(): array
    {
        return [
            'kaisProductId'         => $this->kaisProductId,
            'firmSubdivisionId'     => $this->firmSubdivisionId,
            'storeId'               => $this->storeId,
            'freeQty'               => $this->freeQty,
            'dividedFreeQty'        => $this->dividedFreeQty,
            'retailPriceWithTax'    => $this->retailPriceWithTax,
            'avgIncomePriceWithTax' => $this->avgIncomePriceWithTax,
        ];
    }
}
