<?php

declare(strict_types=1);

namespace App\OffersApi\Repository\TradePointSupplierPriorities;

use App\OffersApi\Domain\Entity\TradePoint\TradePointSupplierPriorities;
use App\OffersApi\Domain\Entity\TradePoint\TradePointSupplierPriority;
use Doctrine\Common\Collections\ArrayCollection;

final class TradePointSupplierPrioritiesRepository
{
    public function __construct(
        private readonly TradePointSupplierPrioritiesAccessObject $dao,
    ) {}

    /**
     * @param array<int> $assortmentUnitIds
     *
     * @return ArrayCollection<int, TradePointSupplierPriorities>
     */
    public function getByKladrIdAndAssortmentUnitIds(string $kladrId, array $assortmentUnitIds): ArrayCollection
    {
        $collection = new ArrayCollection();

        foreach ($this->dao->fetch($kladrId, $assortmentUnitIds) as $item) {
            $collection->set($item['assortmentUnitId'], $this->hydrate($item));
        }

        return $collection;
    }

    /** @param array{'assortmentUnitId': int, 'priorities': string} $item */
    private function hydrate(array $item): TradePointSupplierPriorities
    {
        $priorities = new ArrayCollection();

        foreach (json_decode($item['priorities'], true, 512, JSON_THROW_ON_ERROR) as $priorityData) {
            $priorities->set(
                $priorityData['tradePointId'],
                new TradePointSupplierPriority($priorityData['tradePointId'], array_map('intval', $priorityData['supplierListIds'])),
            );
        }

        return new TradePointSupplierPriorities(
            $item['assortmentUnitId'],
            $priorities,
        );
    }
}
