<?php

namespace App\Tests\OffersApi\Controller;

use App\OffersApi\Controller\OfferController;

class OfferControllerTest extends AbstractOfferControllerTestCase
{
    public function test(): void
    {
        // $dateTimeImmutable = new \DateTimeImmutable('2023-04-05 10:00:00'); // среда

        $offersController = new OfferController($this->createOffersRepository(), $this->createOfferFiltersRepository(), $this->createKaisProductsDatabaseAccessObject(), $this->createOfferDatabaseAccessObject());
        $response         = $offersController->getByAssortmentUnitIdsAndKladrId([111222], '2500000100000');

        $expected = [[
            'assortmentUnitId'    => 111222,
            'price'               => 100.0,
            'priceForPreorder'    => 90.0,
            'priceForWaiting'     => 80.0,
            'discountForPreorder' => 10,
            'discountForWaiting'  => 20,
            'readyTimes'          => [
                [
                    'tradePointId'             => 1,
                    'amountInStorage'          => 10,
                    'amountUnpackedInStorage'  => 0,
                    'amountFromSupplier'       => 99,
                    'amountInStorageByProduct' => [
                        [
                            'productId'      => 999,
                            'amount'         => 6,
                            'amountUnpacked' => 0,
                        ],
                        [
                            'productId'      => 888,
                            'amount'         => 4,
                            'amountUnpacked' => 0,
                        ],
                    ],
                    'readyTimeFromStorage'   => (new \DateTimeImmutable('2023-04-05 11:00:00'))->getTimestamp(),
                    'readyTimeFromSupplier'  => (new \DateTimeImmutable('2023-04-06 12:00:00'))->getTimestamp(),
                    'readyTimeFromSuppliers' => [
                        [
                            'supplierId'      => 10000777,
                            'readyTime'       => (new \DateTimeImmutable('2023-04-07 18:00:00'))->getTimestamp(),
                            'amount'          => 99,
                            'amountByProduct' => [
                                [
                                    'productId'      => 999,
                                    'amount'         => 99,
                                    'amountUnpacked' => 0,
                                ],
                            ],
                        ],
                        [
                            'supplierId'      => 20000888,
                            'readyTime'       => (new \DateTimeImmutable('2023-04-06 12:00:00'))->getTimestamp(),
                            'amount'          => 99,
                            'amountByProduct' => [
                                [
                                    'productId'      => 999,
                                    'amount'         => 99,
                                    'amountUnpacked' => 0,
                                ],
                            ],
                        ],
                    ],
                    'expressAssemblyTime' => null,
                ],
            ],
        ]];

        $this->assertEquals($expected, $this->formatResponse($response));
    }
}
