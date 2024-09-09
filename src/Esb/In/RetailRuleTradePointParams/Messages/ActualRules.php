<?php

declare(strict_types=1);

namespace App\Esb\In\RetailRuleTradePointParams\Messages;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;

final class ActualRules
{
    /** @param ArrayCollection<int, RuleDataTradePointParams> $rules */
    public function __construct(
        #[JMS\XmlList(entry: 'rule-data-tradepoint-params', inline: true)]
        #[JMS\Type('ArrayCollection<App\Esb\In\RetailRuleTradePointParams\Messages\RuleDataTradePointParams>')]
        public readonly ArrayCollection $rules
    ) {}
}
