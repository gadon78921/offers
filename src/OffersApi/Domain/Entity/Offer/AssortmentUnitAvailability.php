<?php

declare(strict_types=1);

namespace App\OffersApi\Domain\Entity\Offer;

use App\OffersApi\Domain\Entity\TradePoint\TradePointWorkTime;

final class AssortmentUnitAvailability
{
    public function __construct(
        public readonly int $tradePointId,
        public readonly int $quantityInTradePoint,
        public readonly int $quantityInTradePointUnpacked,
        public readonly int $quantityFromSuppliers,
        public readonly ?TradePointWorkTime $tradePointWorkTime,
    ) {}

    public function readyTimeFromTradePoint(\DateTimeImmutable $dateTime): ?\DateTimeImmutable
    {
        if (null === $this->tradePointWorkTime || false === $this->tradePointWorkTime->isValid()) {
            return null;
        }

        return $this->tradePointWorkTime->readyTimeFromTradePoint($dateTime);
    }

    public function hasQuantityInTradePoint(): bool
    {
        return $this->quantityInTradePoint > 0 || $this->quantityInTradePointUnpacked > 0;
    }
}
