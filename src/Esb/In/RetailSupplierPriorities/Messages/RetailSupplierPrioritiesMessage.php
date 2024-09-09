<?php

declare(strict_types=1);

namespace App\Esb\In\RetailSupplierPriorities\Messages;

use Monastirevrf\EsbBundle\Messages\EsbMessageInterface;
use Monastirevrf\EsbBundle\Messages\EsbMessageTrait;

final class RetailSupplierPrioritiesMessage implements EsbMessageInterface
{
    use EsbMessageTrait;

    public function __construct(
        public readonly int $regionCode,
        public readonly SupplierPrioritiesData $supplierPrioritiesData,
    ) {}
}
