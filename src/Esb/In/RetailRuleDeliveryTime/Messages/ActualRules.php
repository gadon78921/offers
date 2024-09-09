<?php

declare(strict_types=1);

namespace App\Esb\In\RetailRuleDeliveryTime\Messages;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;

final class ActualRules
{
    /** @param ArrayCollection<int, RuleDataDeliveryTime> $rules */
    public function __construct(
        #[JMS\XmlList(entry: 'rule-data-delivery-time', inline: true)]
        #[JMS\Type('ArrayCollection<App\Esb\In\RetailRuleDeliveryTime\Messages\RuleDataDeliveryTime>')]
        public readonly ArrayCollection $rules
    ) {}
}
