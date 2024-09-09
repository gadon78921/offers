<?php

declare(strict_types=1);

namespace App\OffersBuilding\Repository\OffersForSite;

use App\OffersBuilding\Domain\Entity\Offer;

final class OfferByAssortmentUnitIdRepository extends OfferRepository
{
    protected function getOfferFromCollection(Offer $offer): ?Offer
    {
        return $this->offerByAssortmentUnitIdsCollection->get($offer->assortmentUnitId());
    }

    protected function setOfferInCollection(Offer $offer): void
    {
        $this->offerByAssortmentUnitIdsCollection->set($offer->assortmentUnitId(), $offer);
    }
}
