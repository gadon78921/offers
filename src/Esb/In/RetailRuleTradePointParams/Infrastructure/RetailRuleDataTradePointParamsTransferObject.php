<?php

declare(strict_types=1);

namespace App\Esb\In\RetailRuleTradePointParams\Infrastructure;

final class RetailRuleDataTradePointParamsTransferObject
{
    public function __construct(
        public readonly int $headId,
        public readonly int $ruleId,
        public readonly int $tradePointId,
        public readonly int $workStartHour,
        public readonly int $workEndHour,
        public readonly string $daysOfWork,
        public readonly \DateTime $dateFrom,
    ) {}
}
