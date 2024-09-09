<?php

declare(strict_types=1);

namespace App\OffersBuilding\Repository\TradePoints;

use App\OffersBuilding\Domain\Entity\TradePoint;
use Doctrine\Common\Collections\ArrayCollection;

final class TradePointsRepository
{
    /** @var ArrayCollection<int, TradePoint> */
    private ArrayCollection $collection;

    public function __construct(
        private readonly TradePointsDatabaseAccessObject $dao,
        private readonly TradePointHydrator $hydrator,
    ) {
        $this->collection = new ArrayCollection();
        $this->fill();
    }

    /** @return array<int, non-empty-array<int<0, max>, TradePoint>> */
    public function getTradePointsIndexedByFirmId(string $kladrId): array
    {
        $result = [];
        $this->collection->forAll(function ($key, TradePoint $tradePoint) use (&$result, $kladrId) {
            if ($tradePoint->kladrId === $kladrId) {
                foreach ($tradePoint->firmIds as $firmId) {
                    $result[$firmId][] = $tradePoint;
                }
            }

            return true;
        });

        return $result;
    }

    /** @return array<int, array<int<0, max>, TradePoint>> */
    public function getTradePointsIndexedBySupplierId(string $kladrId): array
    {
        $result = [];
        $this->collection->forAll(function ($key, TradePoint $tradePoint) use (&$result, $kladrId) {
            if ($tradePoint->kladrId === $kladrId) {
                foreach ($tradePoint->supplierIds as $supplierId) {
                    $result[$supplierId][] = $tradePoint;
                }
            }

            return true;
        });

        return $result;
    }

    /** @return array{int} */
    public function getFirmIdsByKladrId(string $kladrId): array
    {
        $firmIds = [];
        $this->collection->forAll(function ($key, TradePoint $tradePoint) use (&$firmIds, $kladrId) {
            if ($tradePoint->kladrId === $kladrId) {
                $firmIds = array_merge($firmIds, $tradePoint->firmIds);
            }

            return true;
        });

        return $firmIds;
    }

    /** @return array{int} */
    public function getSupplierIdsByKladrId(string $kladrId): array
    {
        $supplierIds = [];
        $this->collection->forAll(function ($key, TradePoint $tradePoint) use (&$supplierIds, $kladrId) {
            if ($tradePoint->kladrId === $kladrId) {
                $supplierIds = array_merge($supplierIds, $tradePoint->supplierIds);
            }

            return true;
        });

        return $supplierIds;
    }

    /**
     * @param array{int} $tradePointIds
     *
     * @return array<int, non-empty-array<int<0, max>, TradePoint>>
     */
    public function getTradePointsIndexedByFirmIdByTradePointIds(array $tradePointIds): array
    {
        $result = [];
        $this->collection->forAll(function ($key, TradePoint $tradePoint) use (&$result, $tradePointIds) {
            if (in_array($tradePoint->id, $tradePointIds, true)) {
                foreach ($tradePoint->firmIds as $firmId) {
                    $result[$firmId][] = $tradePoint;
                }
            }

            return true;
        });

        return $result;
    }

    /**
     * @param array{int} $tradePointIds
     *
     * @return array<int, non-empty-array<int<0, max>, TradePoint>>
     */
    public function getTradePointsIndexedBySupplierIdByTradePointIds(array $tradePointIds): array
    {
        $result = [];
        $this->collection->forAll(function ($key, TradePoint $tradePoint) use (&$result, $tradePointIds) {
            if (in_array($tradePoint->id, $tradePointIds, true)) {
                foreach ($tradePoint->supplierIds as $supplierId) {
                    $result[(int) $supplierId][] = $tradePoint;
                }
            }

            return true;
        });

        return $result;
    }

    public function fill(): void
    {
        foreach ($this->dao->fetchTradePoints() as $rawTradePoint) {
            $this->collection->set($rawTradePoint['tradePointId'], $this->hydrator->hydrateTradePoint($rawTradePoint));
        }
    }

    public function getByTradePointId(int $tradePointId): ?TradePoint
    {
        return $this->collection->get($tradePointId);
    }
}
