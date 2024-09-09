<?php

declare(strict_types=1);

namespace App\OffersApi\Domain\Entity\TradePoint;

use Doctrine\Common\Collections\ArrayCollection;

final class SupplierDeliveryTimeRules
{
    public function __construct(
        public int $supplierId,
        /** @var ArrayCollection<int, DeliveryTimeFromSupplierRule> $rules */
        public ArrayCollection $rules,
    ) {}

    /** @param array{array{'firmId': int, 'orderSendTime': string, 'hoursUntilReady': int, 'daysToSendOrders': array{string}}} $rulesData */
    public static function create(int $supplierId, array $rulesData): self
    {
        $rules = new ArrayCollection();
        foreach ($rulesData as $rule) {
            $rules->add(new DeliveryTimeFromSupplierRule(
                $rule['daysToSendOrders'],
                (int) \DateTimeImmutable::createFromFormat('H:i:s', $rule['orderSendTime'])->format('H'),
                $rule['hoursUntilReady'],
                (int) $rule['firmId'],
            ));
        }

        return new self($supplierId, $rules);
    }
}
