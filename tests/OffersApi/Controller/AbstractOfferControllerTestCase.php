<?php

namespace App\Tests\OffersApi\Controller;

use App\OffersApi\Repository\KaisProducts\KaisProductsDatabaseAccessObject;
use App\OffersApi\Repository\Offer\OfferDatabaseAccessObject;
use App\OffersApi\Repository\Offer\OfferRepository;
use App\OffersApi\Repository\OfferDescription\OfferDescriptionDatabaseAccessObject;
use App\OffersApi\Repository\OfferDescription\OfferDescriptionHydrator;
use App\OffersApi\Repository\OfferDescription\OfferDescriptionRepository;
use App\OffersApi\Repository\OfferFilters\OfferFilterHydrator;
use App\OffersApi\Repository\OfferFilters\OfferFiltersDatabaseAccessObject;
use App\OffersApi\Repository\OfferFilters\OfferFiltersRepository;
use App\OffersApi\Repository\Products\OffersWithProductQuantityRepository;
use App\OffersApi\Repository\Products\ProductsDatabaseAccessObject;
use App\OffersApi\Repository\SupplierDeliveryRuleTime\DeliveryTimeFromSupplierDatabaseAccessObject;
use App\OffersApi\Repository\SupplierDeliveryRuleTime\SupplierDeliveryRuleTimeRepository;
use App\OffersApi\Repository\SuppliersForTradePoints\SuppliersForTradePointsAccessObject;
use App\OffersApi\Repository\SuppliersForTradePoints\SuppliersForTradePointsRepository;
use App\OffersApi\Repository\TradePointSupplierPriorities\TradePointSupplierPrioritiesAccessObject;
use App\OffersApi\Repository\TradePointSupplierPriorities\TradePointSupplierPrioritiesRepository;
use App\OffersApi\Repository\TradePointWorkTime\TradePointWorkTimeAccessObject;
use App\OffersApi\Repository\TradePointWorkTime\TradePointWorkTimeRepository;
use App\Tests\AbstractControllerTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;

abstract class AbstractOfferControllerTestCase extends AbstractControllerTestCase
{
    protected function createOffersRepository(): OfferRepository
    {
        return new OfferRepository(
            $this->createOfferDatabaseAccessObject(),
            $this->createTradePointWorkTimeRepository(),
            $this->createOffersWithProductQuantityRepository(),
            $this->createSupplierDeliveryRuleTimeRepository(),
            $this->createTradePointSupplierPrioritiesRepository(),
            $this->createOfferDescriptionRepository(),
        );
    }

    protected function createOfferDatabaseAccessObject(): OfferDatabaseAccessObject
    {
        $offersDatabaseResponse = new ArrayCollection(
            [
                [
                    'assortmentUnitId'     => 111222,
                    'priceWithoutDiscount' => 100.0,
                    'priceForPreorder'     => 90.0,
                    'priceForWaiting'      => 80.0,
                    'discountForPreorder'  => 10,
                    'discountForWaiting'   => 20,
                    'wholesalePrice'       => 70.0,
                    'availability'         => json_encode(
                        [
                            [
                                'tradePointId'              => 1,
                                'quantityInStorage'         => 10,
                                'quantityInStorageUnpacked' => 0,
                                'quantityFromSuppliers'     => 99,
                            ],
                        ],
                        JSON_THROW_ON_ERROR
                    ),
                ],
            ]
        );

        $resultStub     = $this->createStub(Result::class);
        $connectionStub = $this->createStub(Connection::class);
        $connectionStub->method('executeQuery')->willReturn($resultStub);
        $resultStub->method('iterateAssociative')->willReturn($offersDatabaseResponse->getIterator());

        return new OfferDatabaseAccessObject($connectionStub);
    }

    protected function createKaisProductsDatabaseAccessObject(): KaisProductsDatabaseAccessObject
    {
        $connectionStub = $this->createStub(Connection::class);

        return new KaisProductsDatabaseAccessObject($connectionStub);
    }

    protected function createOfferFiltersRepository(): OfferFiltersRepository
    {
        return new OfferFiltersRepository($this->createOfferFiltersDatabaseAccessObject(), new OfferFilterHydrator());
    }

    protected function createOfferFiltersDatabaseAccessObject(): OfferFiltersDatabaseAccessObject
    {
        $connectionStub = $this->createStub(Connection::class);

        return new OfferFiltersDatabaseAccessObject($connectionStub);
    }

    private function createOffersWithProductQuantityRepository(): OffersWithProductQuantityRepository
    {
        $productsDatabaseResponse = new ArrayCollection(
            [
                [
                    'assortmentUnitId'      => 111222,
                    'productsInTradePoints' => json_encode(
                        [
                            [
                                'tradePointId'              => 1,
                                'retailProductId'           => 999,
                                'quantityInStorage'         => 6,
                                'quantityInStorageUnpacked' => 0,
                            ],
                            [
                                'tradePointId'              => 1,
                                'retailProductId'           => 888,
                                'quantityInStorage'         => 4,
                                'quantityInStorageUnpacked' => 0,
                            ],
                        ],
                        JSON_THROW_ON_ERROR
                    ),
                    'productsFromSupplier' => json_encode([
                        [
                            'supplierId'           => 10000777,
                            'retailProductId'      => 999,
                            'quantityFromSupplier' => 99,
                        ],
                        [
                            'supplierId'           => 20000888,
                            'retailProductId'      => 999,
                            'quantityFromSupplier' => 99,
                        ],
                    ], JSON_THROW_ON_ERROR),
                ],
            ]
        );

        $tradePointsWithSupplierIdsFromCityDatabaseResponse = [
            [
                'tradePointId' => 1,
                'supplierIds'  => '{10000777,20000888}',
            ],
        ];

        $resultStub     = $this->createStub(Result::class);
        $connectionStub = $this->createStub(Connection::class);
        $connectionStub->method('executeQuery')->willReturn($resultStub);
        $resultStub->method('iterateAssociative')->willReturn($productsDatabaseResponse->getIterator());
        $resultStub->method('fetchAllAssociative')->willReturn($tradePointsWithSupplierIdsFromCityDatabaseResponse);

        $productsDatabaseAccessObject        = new ProductsDatabaseAccessObject($connectionStub);
        $suppliersForTradePointsAccessObject = new SuppliersForTradePointsAccessObject($connectionStub);
        $suppliersForTradePointsRepository   = new SuppliersForTradePointsRepository($suppliersForTradePointsAccessObject);

        return new OffersWithProductQuantityRepository($productsDatabaseAccessObject, $suppliersForTradePointsRepository);
    }

    private function createSupplierDeliveryRuleTimeRepository(): SupplierDeliveryRuleTimeRepository
    {
        $rulesDatabaseResponse = new ArrayCollection(
            [
                [
                    'supplierId'   => 10000777,
                    'tradePointId' => 1,
                    'rules'        => json_encode([
                        [
                            'daysToSendOrders' => ['Вт', 'Чт'],
                            'orderSendTime'    => '17:00:00',
                            'hoursUntilReady'  => 24,
                        ],
                    ], JSON_THROW_ON_ERROR),
                ],
                [
                    'supplierId'   => 20000888,
                    'tradePointId' => 1,
                    'rules'        => json_encode([
                        [
                            'daysToSendOrders' => ['Ср'],
                            'orderSendTime'    => '11:00:00',
                            'hoursUntilReady'  => 24,
                        ],
                    ], JSON_THROW_ON_ERROR),
                ],
            ]
        );

        $resultStub     = $this->createStub(Result::class);
        $connectionStub = $this->createStub(Connection::class);
        $connectionStub->method('executeQuery')->willReturn($resultStub);
        $resultStub->method('iterateAssociative')->willReturn($rulesDatabaseResponse->getIterator());

        $deliveryTimeFromSupplierDatabaseAccessObject = new DeliveryTimeFromSupplierDatabaseAccessObject($connectionStub);
        $tradePointWorkTimeRepository                 = $this->createTradePointWorkTimeRepository();

        return new SupplierDeliveryRuleTimeRepository($deliveryTimeFromSupplierDatabaseAccessObject, $tradePointWorkTimeRepository);
    }

    private function createTradePointWorkTimeRepository(): TradePointWorkTimeRepository
    {
        $tradePointsWorkTimeDatabaseResponse = [
            [
                'tradePointId'  => 1,
                'workStartHour' => 9,
                'workEndHour'   => 21,
                'daysOfWork'    => 'Пн Вт Ср Чт Пт Сб Вс',
                'validFrom'     => '2023-04-01 00:00:00',
            ],
        ];

        $resultStub     = $this->createStub(Result::class);
        $connectionStub = $this->createStub(Connection::class);
        $connectionStub->method('executeQuery')->willReturn($resultStub);
        $resultStub->method('fetchAllAssociative')->willReturn($tradePointsWorkTimeDatabaseResponse);

        $tradePointWorkTimeAccessObject = new TradePointWorkTimeAccessObject($connectionStub);

        return new TradePointWorkTimeRepository($tradePointWorkTimeAccessObject);
    }

    private function createTradePointSupplierPrioritiesRepository(): TradePointSupplierPrioritiesRepository
    {
        $response = new ArrayCollection([
            [
                'assortmentUnitId' => 111222,
                'priorities'       => json_encode([
                    [
                        'tradePointId'    => 1,
                        'supplierListIds' => [20000888, 10000777],
                    ],
                ], JSON_THROW_ON_ERROR),
            ],
        ]);

        $resultStub     = $this->createStub(Result::class);
        $connectionStub = $this->createStub(Connection::class);
        $connectionStub->method('executeQuery')->willReturn($resultStub);
        $resultStub->method('iterateAssociative')->willReturn($response->getIterator());

        $accessObject = new TradePointSupplierPrioritiesAccessObject($connectionStub);

        return new TradePointSupplierPrioritiesRepository($accessObject);
    }

    private function createOfferDescriptionRepository(): OfferDescriptionRepository
    {
        $offerDescriptionDatabaseResponse = [
            [
                'assortmentUnitId'        => 113665,
                'baseProductIdInUrl'      => 4502,
                'kaisProductId'           => 4615,
                'kaisProductCode'         => 1515,
                'retailProductCode'       => 2323,
                'manufacturerName'        => 'Новартис Консьюмер Хелс С.А.',
                'manufacturerCountryName' => 'Швейцария',
                'productName'             => 'ТераФлю Экстра от гриппа и простуды',
                'fullProductName'         => 'ТераФлю Экстра от гриппа и простуды пор д/р-ра д/приема внутрь пак цефл №10 (яблоко+корица)',
                'subtitle'                => 'порошок для приготовления раствора для приема внутрь пакет цефленовый (яблоко+корица) №10',
                'sellProcedure'           => 'Без ограничений',
                'isVital'                 => false,
                'activeSubstance'         => 'Парацетамол+Фенилэфрин+Фенирамин',
                'canUnpackPrimary'        => false,
                'canUnpackSecondary'      => false,
                'packageQuantity'         => 10,
                'quantityInPrimaryPack'   => 1,
                'quantityInSecondaryPack' => 10,
                'brand'                   => null,
                'dosage'                  => null,
                'dosageForm'              => 'порошок',
                'isStorageTypeCold'       => false,
                'expirationDate'          => 24,
                'prescriptionForm'        => 0,
                'wrappingName'            => 'пакет цефленовый',
            ],
        ];

        $resultStub     = $this->createStub(Result::class);
        $connectionStub = $this->createStub(Connection::class);
        $connectionStub->method('executeQuery')->willReturn($resultStub);
        $resultStub->method('fetchAllAssociative')->willReturn($offerDescriptionDatabaseResponse);

        $offerDescriptionAccessObject = new OfferDescriptionDatabaseAccessObject($connectionStub);

        return new OfferDescriptionRepository($offerDescriptionAccessObject, new OfferDescriptionHydrator());
    }
}
