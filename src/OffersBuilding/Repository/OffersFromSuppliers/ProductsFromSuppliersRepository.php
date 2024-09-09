<?php

declare(strict_types=1);

namespace App\OffersBuilding\Repository\OffersFromSuppliers;

use App\OffersBuilding\Domain\Entity\ProductFromSupplier;
use App\OffersBuilding\Domain\Entity\TradePoint;
use App\OffersBuilding\Repository\RuleSupplierToTradePoint\RuleSupplierToTradePointRepository;
use App\OffersBuilding\Repository\TradePoints\TradePointsRepository;
use Doctrine\Common\Collections\ArrayCollection;

final class ProductsFromSuppliersRepository
{
    /** @var ArrayCollection<int, ProductFromSupplier> */
    private ArrayCollection $collection;

    public function __construct(
        private readonly ProductsFromSuppliersDatabaseAccessObject $dao,
        private readonly ProductFromSupplierHydrator $hydrator,
        private readonly TradePointsRepository $tradePointRepository,
        private readonly RuleSupplierToTradePointRepository $ruleSupplierToTradePointRepository,
    ) {
        $this->collection = new ArrayCollection();
    }

    /**
     * @param array{int}|array{} $assortmentUnitIds
     * @param array{int}|array{} $kaisProductIds
     */
    public function fillByKladrId(string $kladrId, array $assortmentUnitIds = [], array $kaisProductIds = []): void
    {
        $tradePointsBySupplierIds = $this->tradePointRepository->getTradePointsIndexedBySupplierId($kladrId);
        $tradePointsBySupplierIds = $this->filterByRuleDeliveryTime($tradePointsBySupplierIds, $kladrId);
        $this->fill($tradePointsBySupplierIds, $assortmentUnitIds, $kaisProductIds);
    }

    /**
     * @param array<int>         $tradePointIds
     * @param array<int>|array{} $assortmentUnitIds
     * @param array<int>|array{} $kaisProductIds
     */
    public function fillByTradePointIds(array $tradePointIds, array $assortmentUnitIds = [], array $kaisProductIds = []): void
    {
        $tradePointsBySupplierIds = $this->tradePointRepository->getTradePointsIndexedBySupplierIdByTradePointIds($tradePointIds);
        $this->fill($tradePointsBySupplierIds, $assortmentUnitIds, $kaisProductIds);
    }

    /**
     * @param array<int, array<int<0, max>, TradePoint>> $tradePointsBySupplierIds
     * @param array<int>|array{}                         $assortmentUnitIds
     * @param array<int>|array{}                         $kaisProductIds
     */
    private function fill(array $tradePointsBySupplierIds, array $assortmentUnitIds = [], array $kaisProductIds = []): void
    {
        /** @var \Iterator $productIterator */
        $productIterator = $this->dao->fetchProductsFromSuppliers(array_keys($tradePointsBySupplierIds), $assortmentUnitIds, $kaisProductIds);

        while ($productIterator->valid()) {
            $product                = $productIterator->current();
            $product['tradePoints'] = $tradePointsBySupplierIds[(int) $product['supplierId']];
            $this->collection->add($this->hydrator->hydrateProductFromSupplier($product));
            $productIterator->next();
        }
    }

    /**
     * @param array<int, array<int<0, max>, TradePoint>> $tradePointsBySupplierIds
     *
     * @return array<int, array<int<0, max>, TradePoint>>
     */
    private function filterByRuleDeliveryTime(array $tradePointsBySupplierIds, string $kladrId): array
    {
        $rules = empty($tradePointsBySupplierIds) ? [] : $this->ruleSupplierToTradePointRepository->getRulesBySupplierIds(array_keys($tradePointsBySupplierIds), $kladrId);

        foreach ($rules as $rule) {
            $supplierIdFromRule        = $rule['supplier_id'];
            $tradePointIdFromRule      = $rule['trade_point_id'];
            $isForTzOnlyValuesFromRule = json_decode($rule['isForTzOnlyValues'], true, 512, JSON_THROW_ON_ERROR);
            $actualIsForTzOnlyFromRule = $isForTzOnlyValuesFromRule[0]['isForTzOnly'] ?? false;

            $supplierTradePoints = $tradePointsBySupplierIds[$supplierIdFromRule] ?? null;

            if (null === $supplierTradePoints) {
                continue;
            }

            if (null === $tradePointIdFromRule && false === $actualIsForTzOnlyFromRule) {
                continue;
            }

            if (null === $tradePointIdFromRule && true === $actualIsForTzOnlyFromRule) {
                unset($tradePointsBySupplierIds[$supplierIdFromRule]);
                continue;
            }

            foreach ($supplierTradePoints as $index => $tradePoint) {
                if ($tradePoint->id === $tradePointIdFromRule && true === $actualIsForTzOnlyFromRule) {
                    unset($tradePointsBySupplierIds[$supplierIdFromRule][$index]);
                    break;
                }
            }
        }

        return array_filter($tradePointsBySupplierIds);
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
