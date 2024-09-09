<?php

declare(strict_types=1);

namespace App\Tests\OffersApi;

use App\OffersApi\Domain\Entity\Offer\AssortmentUnitAvailability;
use App\OffersApi\Domain\Entity\OfferWithProductQuantity\OfferWithProductsQuantity;
use App\OffersApi\Domain\Entity\OfferWithProductQuantity\ProductsQuantity;
use App\OffersApi\Domain\Entity\OfferWithProductQuantity\QuantityFromSupplier;
use App\OffersApi\Domain\Entity\OfferWithProductQuantity\QuantityProduct;
use App\OffersApi\Domain\Entity\TradePoint\DeliveryTimeFromSupplierRule;
use App\OffersApi\Domain\Entity\TradePoint\SupplierDeliveryTimeRules;
use App\OffersApi\Domain\Entity\TradePoint\TradePointWithSupplierDeliveryTimeRules;
use App\OffersApi\Domain\Entity\TradePoint\TradePointWorkTime;
use Doctrine\Common\Collections\ArrayCollection;

final class DataHelperCase
{
    public static function getQuantityProudct(): QuantityProduct
    {
        return new QuantityProduct(15099, 10, 4);
    }

    public static function getQuantityFromSupplier(): QuantityFromSupplier
    {
        return new QuantityFromSupplier(
            1100270821,
            new ArrayCollection([
                new QuantityProduct(42561, 10, 7),
                new QuantityProduct(28358, 3, 2),
                new QuantityProduct(30389, 4, 0),
            ]),
        );
    }

    public static function getProductsQuantity(): ProductsQuantity
    {
        return new ProductsQuantity(
            10,
            new ArrayCollection([
                self::getQuantityProudct(),
                new QuantityProduct(10029, 3, 0),
            ]),
            new ArrayCollection([
                self::getQuantityFromSupplier(),
                new QuantityFromSupplier(
                    1100009191,
                    new ArrayCollection([
                        new QuantityProduct(8672, 1, 0),
                    ]),
                ),
            ])
        );
    }

    public static function getOfferWithProductsQuantity(): OfferWithProductsQuantity
    {
        return new OfferWithProductsQuantity(
            105733,
            new ArrayCollection([
                self::getProductsQuantity(),
                new ProductsQuantity(
                    3,
                    new ArrayCollection([
                        new QuantityProduct(10029, 5, 1),
                    ]),
                    new ArrayCollection([
                        new QuantityFromSupplier(
                            99900000016,
                            new ArrayCollection([
                                new QuantityProduct(8672, 2, 0),
                            ]),
                        ),
                    ])
                ),
            ]),
        );
    }

    public static function getTradePointWorkTime(): TradePointWorkTime
    {
        return new TradePointWorkTime(
            10,
            9,
            21,
            ['Пн', 'Вт', 'Ср', 'Чт', 'Пт'],
            new \DateTimeImmutable('2023-04-01'),
        );
    }

    public static function getTradePointWithSupplierDeliveryTimeRules(): TradePointWithSupplierDeliveryTimeRules
    {
        return new TradePointWithSupplierDeliveryTimeRules(
            10,
            new ArrayCollection([
                1100009191 => new SupplierDeliveryTimeRules(
                    1100009191,
                    new ArrayCollection([
                        new DeliveryTimeFromSupplierRule(['Пн'], 10, 3, 0),
                        new DeliveryTimeFromSupplierRule(['Вт', 'Пт'], 20, 10, 0),
                    ]),
                ),
                99900000076 => new SupplierDeliveryTimeRules(
                    99900000076,
                    new ArrayCollection([
                        new DeliveryTimeFromSupplierRule(['Ср'], 11, 4, 0),
                        new DeliveryTimeFromSupplierRule(['Сб', 'Вс'], 15, 24, 0),
                    ]),
                ),
            ]),
            self::getTradePointWorkTime(),
        );
    }

    public static function getAssortmentUnitAvailability(): AssortmentUnitAvailability
    {
        return new AssortmentUnitAvailability(
            10,
            25,
            10,
            5,
            self::getTradePointWorkTime(),
        );
    }
}
