<?php

declare(strict_types=1);

namespace App\Esb\In\RetailRuleDeliveryTime\Messages;

use JMS\Serializer\Annotation as JMS;
use Monastirevrf\EsbBundle\Messages\EsbMessageInterface;
use Monastirevrf\EsbBundle\Messages\EsbMessageTrait;

final class RuleDeliveryTimeMessage implements EsbMessageInterface
{
    use EsbMessageTrait;

    public function __construct(
        #[JMS\SerializedName('id')]
        public readonly int $headId,
        public readonly bool $deleted,
        #[JMS\SerializedName('kladrId')]
        public readonly int $regionId,
        public readonly int $supplierId,
        public readonly ?int $firmId,
        public readonly ActualRules $actualRules
    ) {}
}
