<?php

declare(strict_types=1);

namespace App\OffersApi\Repository\OfferDescription;

use App\OffersApi\Domain\Entity\Offer\OfferDescription;
use Doctrine\Common\Collections\ArrayCollection;

final class OfferDescriptionRepository
{
    public function __construct(
        private readonly OfferDescriptionDatabaseAccessObject $dao,
        private readonly OfferDescriptionHydrator $hydrator,
    ) {}

    /**
     * @param array<int> $assortmentUnitIds
     *
     * @return ArrayCollection<int, OfferDescription>
     */
    public function getDescriptionsByAssortmentUnitIds(array $assortmentUnitIds): ArrayCollection
    {
        $collection = new ArrayCollection();
        foreach ($this->dao->fetch($assortmentUnitIds) as $rawOfferDescription) {
            $collection->set($rawOfferDescription['assortmentUnitId'], $this->hydrator->hydrateOfferDescription($rawOfferDescription));
        }

        return $collection;
    }
}
