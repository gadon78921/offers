<?php

declare(strict_types=1);

namespace App\OffersApi\Repository\SupplierDeliveryRuleTime;

use App\OffersApi\Domain\Entity\TradePoint\SupplierDeliveryTimeRules;
use App\OffersApi\Domain\Entity\TradePoint\TradePointWithSupplierDeliveryTimeRules;
use App\OffersApi\Repository\TradePointWorkTime\TradePointWorkTimeRepository;
use Doctrine\Common\Collections\ArrayCollection;

final class SupplierDeliveryRuleTimeRepository
{
    public function __construct(
        private readonly DeliveryTimeFromSupplierDatabaseAccessObject $dao,
        private readonly TradePointWorkTimeRepository $tradePointWorkTimeRepository,
    ) {}

    /**
     * @return ArrayCollection<int, TradePointWithSupplierDeliveryTimeRules>
     */
    public function getByKladrId(string $kladrId): ArrayCollection
    {
        $tradePointIndexedByFirmId = $this->dao->fetchTradePointIndexedByFirmId($kladrId);

        $collection = new ArrayCollection();
        foreach ($this->dao->fetch($kladrId) as $item) {
            $rules = json_decode($item['rules'], true, 512, JSON_THROW_ON_ERROR);

            if (null === $item['firmId'] || 0 === $item['firmId']) {
                $this->addRuleToCollectionForAllTradePoints($collection, $tradePointIndexedByFirmId, $item['supplierId'], $rules);
            }

            $tradePointId = $tradePointIndexedByFirmId[$item['firmId']] ?? null;

            if (null !== $tradePointId) {
                $this->addRuleToCollection($collection, $tradePointId, $item['supplierId'], $rules);
            }
        }

        return $collection;
    }

    /**
     * @param ArrayCollection<int, TradePointWithSupplierDeliveryTimeRules>                                                   $collection
     * @param array<int, int>                                                                                                 $tradePointIndexedByFirmId
     * @param array{array{'firmId': int, 'orderSendTime': string, 'hoursUntilReady': int, 'daysToSendOrders': array{string}}} $rules
     */
    private function addRuleToCollectionForAllTradePoints(ArrayCollection $collection, array $tradePointIndexedByFirmId, int $supplierId, array $rules): void
    {
        foreach ($tradePointIndexedByFirmId as $tradePointId) {
            $this->addRuleToCollection($collection, $tradePointId, $supplierId, $rules);
        }
    }

    /**
     * @param ArrayCollection<int, TradePointWithSupplierDeliveryTimeRules>                                                   $collection
     * @param array{array{'firmId': int, 'orderSendTime': string, 'hoursUntilReady': int, 'daysToSendOrders': array{string}}} $rules
     */
    private function addRuleToCollection(ArrayCollection $collection, int $tradePointId, int $supplierId, array $rules): void
    {
        $tradePointWithSupplierDeliveryTimeRules = $collection->get($tradePointId);

        if (null === $tradePointWithSupplierDeliveryTimeRules) {
            $tradePointWithSupplierDeliveryTimeRules = new TradePointWithSupplierDeliveryTimeRules(
                $tradePointId,
                new ArrayCollection(),
                $this->tradePointWorkTimeRepository->get($tradePointId),
            );
            $collection->set($tradePointId, $tradePointWithSupplierDeliveryTimeRules);
        }

        $tradePointWithSupplierDeliveryTimeRules->supplierDeliveryTimeRules->set($supplierId, SupplierDeliveryTimeRules::create($supplierId, $rules));
    }
}
