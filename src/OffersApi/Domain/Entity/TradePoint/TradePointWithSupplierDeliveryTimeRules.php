<?php

declare(strict_types=1);

namespace App\OffersApi\Domain\Entity\TradePoint;

use Doctrine\Common\Collections\ArrayCollection;

final class TradePointWithSupplierDeliveryTimeRules
{
    private const DEFAULT_HOURS_TO_ASSEMBLE_FROM_SUPPLIER = 36;

    public function __construct(
        public int $tradePointId,
        /** @var ArrayCollection<int, SupplierDeliveryTimeRules> $supplierDeliveryTimeRules */
        public ArrayCollection $supplierDeliveryTimeRules,
        public ?TradePointWorkTime $tradePointWorkTime,
    ) {}

    public function readyTimeFromTradePointForSupplierId(int $supplierId, \DateTimeImmutable $dateTime): ?\DateTimeImmutable
    {
        if (null === $this->tradePointWorkTime || false === $this->tradePointWorkTime->isValid()) {
            return null;
        }

        $rules       = $this->supplierDeliveryTimeRules->get($supplierId);
        $actualRules = null === $rules ? [] : $this->getActualRules($rules->rules);

        /** @var DeliveryTimeFromSupplierRule $rule */
        foreach ($actualRules as $rule) {
            $readyTimeFromSupplier      = $rule->readyTimeFromSupplier($dateTime);
            $readyTimeFromTradePoints[] = $this->tradePointWorkTime->readyTimeFromTradePoint($readyTimeFromSupplier);
        }

        return empty($readyTimeFromTradePoints) ? $this->defaultReadyTimeFromSupplier() : min($readyTimeFromTradePoints);
    }

    /**
     * @param ArrayCollection<int, DeliveryTimeFromSupplierRule> $rules
     *
     * @return ArrayCollection<int, DeliveryTimeFromSupplierRule>
     */
    private function getActualRules(ArrayCollection $rules): ArrayCollection
    {
        $rulesDataSpecificForTradePoint = $rules->filter(static fn(DeliveryTimeFromSupplierRule $rule) => 0 !== $rule->ruleForFirmId);

        return $rulesDataSpecificForTradePoint->isEmpty() ? $rules : $rulesDataSpecificForTradePoint;
    }

    private function defaultReadyTimeFromSupplier(): \DateTimeImmutable
    {
        $readyTimeFromSupplier = (new \DateTimeImmutable())->modify('+' . self::DEFAULT_HOURS_TO_ASSEMBLE_FROM_SUPPLIER . ' hours');

        return $this->tradePointWorkTime->readyTimeFromTradePoint($readyTimeFromSupplier);
    }
}
