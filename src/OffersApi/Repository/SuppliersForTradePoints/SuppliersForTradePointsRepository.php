<?php

declare(strict_types=1);

namespace App\OffersApi\Repository\SuppliersForTradePoints;

use App\OffersApi\Domain\Entity\TradePoint\SuppliersForTradePoint;
use Doctrine\Common\Collections\ArrayCollection;

final class SuppliersForTradePointsRepository
{
    /** @var ArrayCollection<int, SuppliersForTradePoint>|null */
    private ?ArrayCollection $collection = null;

    public function __construct(
        private readonly SuppliersForTradePointsAccessObject $dao,
    ) {}

    public function get(int $tradePointId): ?SuppliersForTradePoint
    {
        if (null === $this->collection) {
            $this->fill($tradePointId);
        }

        return $this->collection->get($tradePointId);
    }

    private function fill(int $tradePointId): void
    {
        $this->collection = new ArrayCollection();

        foreach ($this->dao->fetchAllTradePointsWithSupplierIdsFromCity($tradePointId) as $raw) {
            $this->collection->set(
                $raw['tradePointId'],
                $this->hydrate($raw),
            );
        }
    }

    /** @param array{'tradePointId': int, 'supplierIds': array<int>} $raw */
    private function hydrate(array $raw): SuppliersForTradePoint
    {
        return new SuppliersForTradePoint(
            $raw['tradePointId'],
            $raw['supplierIds'],
        );
    }
}
