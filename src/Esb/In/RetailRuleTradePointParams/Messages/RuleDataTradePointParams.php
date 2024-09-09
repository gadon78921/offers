<?php

declare(strict_types=1);

namespace App\Esb\In\RetailRuleTradePointParams\Messages;

use JMS\Serializer\Annotation as JMS;

final class RuleDataTradePointParams
{
    public function __construct(
        #[JMS\SerializedName('id')]
        public readonly int $ruleId,
        public readonly bool $deleted,
        public readonly int $workStartHour,
        public readonly int $workEndHour,
        public readonly string $daysOfWork,
        #[JMS\Type("DateTime<'Y-m-d\TH:i:s'>")]
        public readonly \DateTime $dateFrom,
    ) {}
}
