<?php

declare(strict_types=1);

namespace App\Esb\In\RetailStocks\Messages;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;

final class RetailStockRowsCollection
{
    /** @param ArrayCollection<int, RetailStockRow> $retailStockRows */
    public function __construct(
        #[JMS\XmlList(['inline' => true, 'entry' => 'retail-stock-row'])]
        #[JMS\Type('ArrayCollection<App\Esb\In\RetailStocks\Messages\RetailStockRow>')]
        public readonly ArrayCollection $retailStockRows
    ) {}
}
