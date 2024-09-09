<?php

declare(strict_types=1);

namespace App\Esb\In\RetailOrderCityPrices\Messages;

use Monastirevrf\EsbBundle\Messages\EsbMessageInterface;
use Monastirevrf\EsbBundle\Messages\EsbMessageTrait;

final class RetailOrderCityPricesMessage implements EsbMessageInterface
{
    use EsbMessageTrait;

    public function __construct(
        public readonly string $cityKladrId,
        public readonly OrderPricesData $orderPricesData
    ) {}
}
