<?php

declare(strict_types=1);

namespace App\Esb\In\RetailStocks\Messages;

use JMS\Serializer\Annotation as JMS;

final class RetailStockRow
{
    public function __construct(
        #[JMS\SerializedName('KAISProductId')]
        public readonly int $kaisProductId,
        public readonly float $retailPriceWithTax,
        public readonly float $avgIncomePriceWithTax,
        public readonly int $freeQty,
        public readonly int $dividedFreeQty,
        public readonly bool $isDiscount,
    ) {}
}
