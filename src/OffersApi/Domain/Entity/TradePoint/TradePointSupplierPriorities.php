<?php

declare(strict_types=1);

namespace App\OffersApi\Domain\Entity\TradePoint;

use Doctrine\Common\Collections\ArrayCollection;

final class TradePointSupplierPriorities
{
    public function __construct(
        public int $assortmentUnitId,
        /** @var ArrayCollection<int, TradePointSupplierPriority> $priorities */
        public ArrayCollection $priorities,
    ) {}
}
