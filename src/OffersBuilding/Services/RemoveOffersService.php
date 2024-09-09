<?php

declare(strict_types=1);

namespace App\OffersBuilding\Services;

use App\OffersBuilding\Gateway\OffersForSiteGateway;
use App\OffersBuilding\Gateway\ProductGateway;

final class RemoveOffersService
{
    public function __construct(
        private readonly OffersForSiteGateway $offerGateway,
        private readonly ProductGateway $productGateway,
    ) {}

    /** @param array<int> $assortmentUnitIds */
    public function removeByAssortmentUnitIds(array $assortmentUnitIds): void
    {
        $this->offerGateway->removeByAssortmentUnitIds($assortmentUnitIds);
        $this->productGateway->removeByAssortmentUnitIds($assortmentUnitIds);
    }

    /** @param array<int> $tradePointIds */
    public function removeByTradePointIds(array $tradePointIds): void
    {
        $this->offerGateway->removeByTradePointIds($tradePointIds);
        $this->productGateway->removeByTradePointIds($tradePointIds);
    }
}
