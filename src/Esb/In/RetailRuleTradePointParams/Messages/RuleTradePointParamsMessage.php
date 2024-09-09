<?php

declare(strict_types=1);

namespace App\Esb\In\RetailRuleTradePointParams\Messages;

use JMS\Serializer\Annotation as JMS;
use Monastirevrf\EsbBundle\Messages\EsbMessageInterface;
use Monastirevrf\EsbBundle\Messages\EsbMessageTrait;

final class RuleTradePointParamsMessage implements EsbMessageInterface
{
    use EsbMessageTrait;

    public function __construct(
        #[JMS\SerializedName('id')]
        public readonly int $headId,
        public readonly bool $deleted,
        public readonly ?int $tradePointId,
        public readonly ActualRules $actualRules,
    ) {}
}
