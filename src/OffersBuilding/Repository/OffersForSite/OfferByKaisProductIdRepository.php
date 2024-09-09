<?php

declare(strict_types=1);

namespace App\OffersBuilding\Repository\OffersForSite;

use App\OffersBuilding\Domain\Entity\Offer;

final class OfferByKaisProductIdRepository extends OfferRepository
{
    protected function getOfferFromCollection(Offer $offer): ?Offer
    {
        return $this->offerByAssortmentUnitIdsCollection->get($offer->kaisProductId());
    }

    protected function setOfferInCollection(Offer $offer): void
    {
        $this->offerByAssortmentUnitIdsCollection->set($offer->kaisProductId(), $offer);
    }
}
