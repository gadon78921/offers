<?php

declare(strict_types=1);

namespace App\Esb\In\RetailRuleCustomerOrderReadyTime\Messages;

use JMS\Serializer\Annotation as JMS;
use Monastirevrf\EsbBundle\Messages\EsbMessageInterface;
use Monastirevrf\EsbBundle\Messages\EsbMessageTrait;

final class RetailRuleCustomerOrderReadyTimeMessage implements EsbMessageInterface
{
    use EsbMessageTrait;

    /** @param array<string> $daysToSendOrders */
    public function __construct(
        #[JMS\SerializedName('id')]
        public readonly int $headId,
        public readonly bool $deleted,
        #[JMS\SerializedName('kladrId')]
        public readonly int $regionId,
        public readonly int $supplierId,
        public readonly ?int $firmId,
        public readonly string $daysOfWeekToSendOrders,
        public array $daysToSendOrders,
        #[JMS\Type("DateTime<'H:i:s'>")]
        public readonly \DateTime $orderSendTime,
        public readonly ActualRules $actualRules
    ) {}

    #[JMS\PostDeserialize]
    public function postDeserialize(): void
    {
        $daysOfWeekToSendOrders = trim($this->daysOfWeekToSendOrders);
        $this->daysToSendOrders = explode(' ', $daysOfWeekToSendOrders);
    }
}
