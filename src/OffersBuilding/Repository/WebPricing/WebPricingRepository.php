<?php

declare(strict_types=1);

namespace App\OffersBuilding\Repository\WebPricing;

use App\OffersBuilding\Domain\ValueObject\WebPricing;
use Doctrine\Common\Collections\ArrayCollection;

final class WebPricingRepository
{
    /** @var ArrayCollection<int, WebPricing> */
    private ArrayCollection $collection;

    public function __construct(
        private readonly WebPricingDatabaseAccessObject $dao,
        private readonly WebPricingHydrator $hydrator,
    ) {
        $this->collection = new ArrayCollection();
    }

    /** @param array<int> $assortmentUnitIds */
    public function fillByKladrId(string $kladrId, array $assortmentUnitIds = []): void
    {
        foreach ($this->dao->fetchWebPricing($kladrId, $assortmentUnitIds) as $price) {
            $this->collection->set((int) $price['assortmentUnitId'], $this->hydrator->hydrateWebPricing($price));
        }
    }

    public function getByAssortmentUnitId(int $assortmentUnitId): ?WebPricing
    {
        return $this->collection->get($assortmentUnitId);
    }

    public function clear(): void
    {
        $this->collection = new ArrayCollection();
    }
}
