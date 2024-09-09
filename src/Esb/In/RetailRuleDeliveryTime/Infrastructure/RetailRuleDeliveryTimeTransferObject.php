<?php

declare(strict_types=1);

namespace App\Esb\In\RetailRuleDeliveryTime\Infrastructure;

final class RetailRuleDeliveryTimeTransferObject
{
    public function __construct(
        public readonly int $headId,
        public readonly int $ruleId,
        public readonly int $regionId,
        public readonly int $supplierId,
        public readonly ?int $firmId,
        public readonly bool $isForTZOnly,
        public readonly \DateTime $dateFrom,
    ) {}
}
