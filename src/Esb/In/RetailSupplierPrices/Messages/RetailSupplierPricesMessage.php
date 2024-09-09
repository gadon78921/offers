<?php

declare(strict_types=1);

namespace App\Esb\In\RetailSupplierPrices\Messages;

use Monastirevrf\EsbBundle\Messages\EsbMessageInterface;
use Monastirevrf\EsbBundle\Messages\EsbMessageTrait;

final class RetailSupplierPricesMessage implements EsbMessageInterface
{
    use EsbMessageTrait;

    public function __construct(
        public readonly int $supplierId,
        public readonly SupplierPricesData $supplierPricesData,
    ) {}
}
