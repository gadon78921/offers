<?php

declare(strict_types=1);

namespace App\Esb\In\RetailStocks\Messages;

use Monastirevrf\EsbBundle\Messages\EsbMessageInterface;
use Monastirevrf\EsbBundle\Messages\EsbMessageTrait;

final class RetailStocksMessage implements EsbMessageInterface
{
    use EsbMessageTrait;

    public function __construct(
        public readonly string $firmSubdivisionId,
        public readonly int $storeId,
        public readonly RetailStockRowsCollection $rows,
    ) {}
}
