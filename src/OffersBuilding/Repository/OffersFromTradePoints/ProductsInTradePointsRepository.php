<?php

declare(strict_types=1);

namespace App\OffersBuilding\Repository\OffersFromTradePoints;

use App\OffersBuilding\Domain\Entity\ProductInTradePoint;
use App\OffersBuilding\Domain\Entity\TradePoint;
use App\OffersBuilding\Repository\TradePoints\TradePointsRepository;
use Doctrine\Common\Collections\ArrayCollection;

final class ProductsInTradePointsRepository
{
    /** @var ArrayCollection<int, ProductInTradePoint> */
    private ArrayCollection $collection;

    public function __construct(
        private readonly ProductsInTradePointsDatabaseAccessObject $dao,
        private readonly ProductInTradePointHydrator $hydrator,
        private readonly TradePointsRepository $tradePointRepository,
    ) {
        $this->collection = new ArrayCollection();
    }

    /**
     * @param array<int> $assortmentUnitIds
     * @param array<int> $kaisProductIds
     */
    public function fillByKladrId(string $kladrId, array $assortmentUnitIds = [], array $kaisProductIds = []): void
    {
        $tradePointsByFirmIds = $this->tradePointRepository->getTradePointsIndexedByFirmId($kladrId);
        $this->fill($tradePointsByFirmIds, $assortmentUnitIds, $kaisProductIds);
    }

    /**
     * @param array<int> $tradePointIds
     * @param array<int> $assortmentUnitIds
     * @param array<int> $kaisProductIds
     */
    public function fillByTradePointIds(array $tradePointIds, array $assortmentUnitIds = [], array $kaisProductIds = []): void
    {
        $tradePointsByFirmIds = $this->tradePointRepository->getTradePointsIndexedByFirmIdByTradePointIds($tradePointIds);
        $this->fill($tradePointsByFirmIds, $assortmentUnitIds, $kaisProductIds);
    }

    /**
     * @param array<int, non-empty-array<int<0, max>, TradePoint>> $tradePointsByFirmIds
     * @param array<int>                                           $assortmentUnitIds
     * @param array<int>                                           $kaisProductIds
     */
    private function fill(array $tradePointsByFirmIds, array $assortmentUnitIds = [], array $kaisProductIds = []): void
    {
        /** @var \Iterator $productIterator */
        $productIterator = $this->dao->fetchProductsInFirms(array_keys($tradePointsByFirmIds), $assortmentUnitIds, $kaisProductIds);

        while ($productIterator->valid()) {
            $product = $productIterator->current();

            foreach ($tradePointsByFirmIds[(int) $product['firmId']] as $tradePoint) {
                $product['tradePoint'] = $tradePoint;
                $this->collection->add($this->hydrator->hydrateProductInTradePoint($product));
            }

            $productIterator->next();
        }
    }

    public function forAll(\Closure $func): bool
    {
        return $this->collection->forAll($func);
    }

    public function clear(): void
    {
        $this->collection = new ArrayCollection();
    }
}
