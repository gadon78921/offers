<?php

declare(strict_types=1);

namespace App\OffersApi\Repository\Offer;

use App\OffersApi\Domain\Entity\Offer\AssortmentUnitAvailability;
use App\OffersApi\Domain\Entity\Offer\Offer;
use App\OffersApi\Domain\Entity\Offer\OfferReadyTime;
use App\OffersApi\Domain\Entity\OfferWithProductQuantity\ProductsQuantity;
use App\OffersApi\Domain\Entity\TradePoint\TradePointSupplierPriority;
use App\OffersApi\Domain\Entity\TradePoint\TradePointWithSupplierDeliveryTimeRules;
use App\OffersApi\Repository\OfferDescription\OfferDescriptionRepository;
use App\OffersApi\Repository\Products\OffersWithProductQuantityRepository;
use App\OffersApi\Repository\SupplierDeliveryRuleTime\SupplierDeliveryRuleTimeRepository;
use App\OffersApi\Repository\TradePointSupplierPriorities\TradePointSupplierPrioritiesRepository;
use App\OffersApi\Repository\TradePointWorkTime\TradePointWorkTimeRepository;
use Doctrine\Common\Collections\ArrayCollection;

final class OfferRepository
{
    public function __construct(
        private readonly OfferDatabaseAccessObject $dao,
        private readonly TradePointWorkTimeRepository $tradePointWorkTimeRepository,
        private readonly OffersWithProductQuantityRepository $offersWithProductQuantityRepository,
        private readonly SupplierDeliveryRuleTimeRepository $supplierDeliveryRuleTimeRepository,
        private readonly TradePointSupplierPrioritiesRepository $tradePointSupplierPrioritiesRepository,
        private readonly OfferDescriptionRepository $offerDescriptionRepository,
    ) {}

    /**
     * @param array<int> $assortmentUnitIds
     *
     * @return array{'totalCount': int, 'offers': ArrayCollection<int, Offer>}
     */
    public function getOffers(
        ?string $kladrId,
        array $assortmentUnitIds,
        int $limit = 1000,
        int $offset = 0,
        ?string $sortBy = null,
        string $sortOrder = 'ASC',
    ): array {
        $totalCount = $this->dao->fetchTotalCount($kladrId, $assortmentUnitIds);
        $offersData = $this->dao->fetch($kladrId, $assortmentUnitIds, $limit, $offset, $sortBy, $sortOrder);

        return $this->createOffersResponse($offersData, $kladrId, $totalCount);
    }

    /**
     * @param array<int> $retailProductIds
     *
     * @return array{'totalCount': int, 'offers': ArrayCollection<int, Offer>}
     */
    public function getOffersByRetailProductIdsAndKladrId(?string $kladrId, array $retailProductIds): array
    {
        $offersData                         = $this->dao->fetchByRetailProductIdsAndKladrId($kladrId, $retailProductIds);
        $offersWithTotalCount               = $this->createOffersResponse($offersData, $kladrId, 0);
        $offersWithTotalCount['totalCount'] = $offersWithTotalCount['offers']->count();

        return $offersWithTotalCount;
    }

    public function isEndCategory(int $category, string $kladrId): bool
    {
        return $this->dao->isEndCategory($category, $kladrId);
    }

    /**
     * @return array{'totalCount': int, 'offers': ArrayCollection<int, Offer>}
     */
    public function getOffersByBaseProductIdAndKladrId(
        int $baseProductId,
        string $kladrId,
    ): array {
        $offersData = $this->dao->fetchByBaseProductIdAndKladrId($baseProductId, $kladrId);

        return $this->createOffersResponse($offersData, $kladrId, 1);
    }

    /**
     * @param array<int> $categoryIds
     *
     * @return array{'totalCount': int, 'offers': ArrayCollection<int, Offer>, 'totalCountOffersByCategoryIds': array<int, int>}
     */
    public function getOffersGroupedByCategoryIdAndKladrId(array $categoryIds, string $kladrId, int $limitInEachCategory): array
    {
        $offersData                                            = $this->dao->fetchByCategoryIdsAndKladrIdGroupedByCategoryId($categoryIds, $kladrId, $limitInEachCategory);
        $offersWithTotalCount                                  = $this->createOffersResponse($offersData, $kladrId, 0);
        $offersWithTotalCount['totalCount']                    = $offersWithTotalCount['offers']->count();
        $offersWithTotalCount['totalCountOffersByCategoryIds'] = $this->dao->fetchTotalCountByCategoryIds($categoryIds, $kladrId);

        return $offersWithTotalCount;
    }

    /**
     * @param \Traversable<int, array{'assortmentUnitId': int, 'priceWithoutDiscount': float, 'priceForPreorder': float, 'priceForWaiting': float, 'discountForPreorder': int, 'discountForWaiting': int, 'wholesalePrice': float, 'availability': string}> $offersData
     *
     * @return array{'totalCount': int, 'offers': ArrayCollection<int, Offer>}
     */
    private function createOffersResponse(\Traversable $offersData, ?string $kladrId, int $totalCount): array
    {
        $offersCollection             = new ArrayCollection();
        $offersAvailabilityCollection = new ArrayCollection();

        foreach ($offersData as $offerData) {
            $offer = new Offer(
                (int) $offerData['assortmentUnitId'],
                (float) $offerData['priceWithoutDiscount'],
                (float) $offerData['priceForPreorder'],
                (float) $offerData['priceForWaiting'],
                (int) $offerData['discountForPreorder'],
                (int) $offerData['discountForWaiting'],
                (float) $offerData['wholesalePrice'],
                new ArrayCollection(),
            );

            $offersCollection->set($offerData['assortmentUnitId'], $offer);
            $offersAvailabilityCollection->set($offerData['assortmentUnitId'], json_decode($offerData['availability'], true, 512, JSON_THROW_ON_ERROR));
        }

        if (false === $offersCollection->isEmpty()) {
            $offersAssortmentUnitIds           = $offersCollection->getKeys();
            $offersWithProductQuantity         = null === $kladrId ? new ArrayCollection() : $this->offersWithProductQuantityRepository->getByKladrIdAndAssortmentUnitIds($kladrId, $offersAssortmentUnitIds);
            $supplierDeliveryRulesByTradePoint = null === $kladrId ? new ArrayCollection() : $this->supplierDeliveryRuleTimeRepository->getByKladrId($kladrId);
            $tradePointSupplierPriorities      = null === $kladrId ? new ArrayCollection() : $this->tradePointSupplierPrioritiesRepository->getByKladrIdAndAssortmentUnitIds($kladrId, $offersAssortmentUnitIds);
            $offersDescriptions                = $this->offerDescriptionRepository->getDescriptionsByAssortmentUnitIds($offersAssortmentUnitIds);

            $offersCollection->map(function (Offer $offer) use ($offersAvailabilityCollection, $offersWithProductQuantity, $supplierDeliveryRulesByTradePoint, $tradePointSupplierPriorities, $kladrId) {
                $offerAvailability = $offersAvailabilityCollection->get($offer->assortmentUnitId);

                if (null === $offerAvailability || null === $kladrId) {
                    return;
                }

                /** @var array{'tradePointId': int, 'quantityInStorage': int, 'quantityInStorageUnpacked': int, 'quantityFromSuppliers': int} $availabilityData */
                foreach ($offerAvailability as $availabilityData) {
                    $offer->readyTimes->set(
                        $availabilityData['tradePointId'],
                        $this->prepareReadyTime(
                            $availabilityData,
                            $offersWithProductQuantity->get($offer->assortmentUnitId)?->productsQuantity ?? new ArrayCollection(),
                            $supplierDeliveryRulesByTradePoint,
                            $tradePointSupplierPriorities->get($offer->assortmentUnitId)?->priorities ?? new ArrayCollection(),
                        )
                    );
                }
            });
            $offersCollection->map(fn(Offer $offer) => $offer->offerDescription = $offersDescriptions->get($offer->assortmentUnitId));
        }

        return [
            'totalCount' => $totalCount,
            'offers'     => $offersCollection,
        ];
    }

    /**
     * @param array{'tradePointId': int, 'quantityInStorage': int, 'quantityInStorageUnpacked': int, 'quantityFromSuppliers': int} $availabilityData
     * @param ArrayCollection<int, ProductsQuantity>                                                                               $productsQuantityInTradePoints
     * @param ArrayCollection<int, TradePointWithSupplierDeliveryTimeRules>                                                        $supplierDeliveryRulesByTradePoint
     * @param ArrayCollection<int, TradePointSupplierPriority>                                                                     $tradePointSupplierPriorities
     */
    private function prepareReadyTime(
        array $availabilityData,
        ArrayCollection $productsQuantityInTradePoints,
        ArrayCollection $supplierDeliveryRulesByTradePoint,
        ArrayCollection $tradePointSupplierPriorities,
        ?\DateTimeImmutable $dateTime = null,
    ): OfferReadyTime {
        $availability = new AssortmentUnitAvailability(
            $availabilityData['tradePointId'],
            $availabilityData['quantityInStorage'],
            $availabilityData['quantityInStorageUnpacked'],
            $availabilityData['quantityFromSuppliers'],
            $this->tradePointWorkTimeRepository->get($availabilityData['tradePointId']),
        );

        return OfferReadyTime::create(
            $availability,
            $productsQuantityInTradePoints->get($availability->tradePointId),
            $supplierDeliveryRulesByTradePoint->get($availability->tradePointId),
            $tradePointSupplierPriorities->get($availability->tradePointId),
            $dateTime ?? new \DateTimeImmutable(),
        );
    }
}
