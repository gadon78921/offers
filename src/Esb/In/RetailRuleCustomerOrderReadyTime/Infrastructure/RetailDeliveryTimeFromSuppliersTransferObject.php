<?php

declare(strict_types=1);

namespace App\Esb\In\RetailRuleCustomerOrderReadyTime\Infrastructure;

final class RetailDeliveryTimeFromSuppliersTransferObject
{
    /** @param array<string> $daysToSendOrders */
    public function __construct(
        public readonly int $headId,
        public readonly int $ruleId,
        public readonly int $regionId,
        public readonly int $supplierId,
        public readonly ?int $firmId,
        public readonly array $daysToSendOrders,
        public readonly \DateTime $orderSendTime,
        public readonly int $hoursUntilReady,
        public readonly \DateTime $dateFrom,
    ) {}
}
