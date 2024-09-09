<?php

declare(strict_types=1);

namespace App\OffersApi\Domain\Entity\Offer;

use App\OffersApi\Domain\Entity\OfferWithProductQuantity\ProductsQuantity;
use App\OffersApi\Domain\Entity\OfferWithProductQuantity\QuantityFromSupplier;
use App\OffersApi\Domain\Entity\OfferWithProductQuantity\QuantityProduct;
use App\OffersApi\Domain\Entity\TradePoint\TradePointSupplierPriority;
use App\OffersApi\Domain\Entity\TradePoint\TradePointWithSupplierDeliveryTimeRules;
use Doctrine\Common\Collections\ArrayCollection;

final class OfferReadyTime
{
    /**
     * @param ArrayCollection<int, OfferAmountInStorage>   $amountInStorageByProduct
     * @param ArrayCollection<int, OfferSupplierReadyTime> $readyTimeFromSuppliers
     */
    private function __construct(
        public int $tradePointId,
        public int $amountInStorage,
        public int $amountUnpackedInStorage,
        public int $amountFromSupplier,
        public ArrayCollection $amountInStorageByProduct,
        public ?int $readyTimeFromStorage,
        public ?int $readyTimeFromSupplier,
        public ArrayCollection $readyTimeFromSuppliers,
        public ?int $maxOrderableAmount,
        public ?int $expressAssemblyTime,
    ) {}

    public static function create(
        AssortmentUnitAvailability $availability,
        ProductsQuantity $productsByTradePoint,
        ?TradePointWithSupplierDeliveryTimeRules $tradePointWithSupplierDeliveryTimeRules,
        ?TradePointSupplierPriority $tradePointSupplierPriority,
        \DateTimeImmutable $dateTime,
    ): self {
        $readyTimeFromSuppliers = self::prepareReadyTimeFromSuppliers($tradePointWithSupplierDeliveryTimeRules, $productsByTradePoint->quantityFromSuppliers, $dateTime);

        return new self(
            $availability->tradePointId,
            $availability->quantityInTradePoint,
            $availability->quantityInTradePointUnpacked,
            $availability->quantityFromSuppliers,
            $availability->hasQuantityInTradePoint() ? self::prepareAmountInStorageByProduct($productsByTradePoint->quantityInTradePoint) : new ArrayCollection(),
            $availability->hasQuantityInTradePoint() ? $availability->readyTimeFromTradePoint($dateTime)?->getTimestamp() : null,
            self::prepareReadyTimeFromSupplier($readyTimeFromSuppliers, $tradePointSupplierPriority),
            $readyTimeFromSuppliers,
            null,
            null,
        );
    }

    /**
     * @param ArrayCollection<int, QuantityFromSupplier> $productsBySupplier
     *
     * @return ArrayCollection<int, OfferSupplierReadyTime>
     */
    private static function prepareReadyTimeFromSuppliers(
        ?TradePointWithSupplierDeliveryTimeRules $tradePointWithSupplierDeliveryTimeRules,
        ArrayCollection $productsBySupplier,
        \DateTimeImmutable $dateTime,
    ): ArrayCollection {
        $result = new ArrayCollection();

        foreach ($productsBySupplier as $product) {
            if ($product->totalQuantityFromSupplier() > 0) {
                $result->add(
                    OfferSupplierReadyTime::create($product, $tradePointWithSupplierDeliveryTimeRules, $dateTime),
                );
            }
        }

        return $result;
    }

    /**
     * @param ArrayCollection<int, OfferSupplierReadyTime> $readyTimeFromSuppliers
     */
    private static function prepareReadyTimeFromSupplier(
        ArrayCollection $readyTimeFromSuppliers,
        ?TradePointSupplierPriority $tradePointSupplierPriority,
    ): ?int {
        if ($readyTimeFromSuppliers->count() < 1) {
            return null;
        }

        if (null !== $tradePointSupplierPriority) {
            foreach ($tradePointSupplierPriority->supplierIds as $supplierId) {
                foreach ($readyTimeFromSuppliers as $readyTimeFromSupplier) {
                    if ($readyTimeFromSupplier->supplierId === $supplierId) {
                        return $readyTimeFromSupplier->readyTime;
                    }
                }
            }
        }

        return $readyTimeFromSuppliers->first()->readyTime;
    }

    /**
     * @param ArrayCollection<int, QuantityProduct> $productsByTradePoint
     *
     * @return ArrayCollection<int, OfferAmountInStorage>
     */
    private static function prepareAmountInStorageByProduct(ArrayCollection $productsByTradePoint): ArrayCollection
    {
        return $productsByTradePoint->map(
            fn(QuantityProduct $product) => OfferAmountInStorage::createFromQuantityProduct($product),
        );
    }
}
