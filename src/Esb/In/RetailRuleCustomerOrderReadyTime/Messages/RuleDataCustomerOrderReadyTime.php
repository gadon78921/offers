<?php

declare(strict_types=1);

namespace App\Esb\In\RetailRuleCustomerOrderReadyTime\Messages;

use JMS\Serializer\Annotation as JMS;

final class RuleDataCustomerOrderReadyTime
{
    public function __construct(
        #[JMS\SerializedName('id')]
        public readonly int $ruleId,
        public readonly bool $deleted,
        public readonly int $hoursUntilReady,
        #[JMS\Type("DateTime<'Y-m-d\TH:i:s'>")]
        public readonly \DateTime $dateFrom,
    ) {}
}
