<?php

declare(strict_types=1);

namespace App\Esb\In\RetailRuleCustomerOrderReadyTime\Messages;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;

final class ActualRules
{
    /** @param ArrayCollection<int, RuleDataCustomerOrderReadyTime> $rules */
    public function __construct(
        #[JMS\XmlList(entry: 'rule-data-customer-order-ready-time', inline: true)]
        #[JMS\Type('ArrayCollection<App\Esb\In\RetailRuleCustomerOrderReadyTime\Messages\RuleDataCustomerOrderReadyTime>')]
        public readonly ArrayCollection $rules
    ) {}
}
