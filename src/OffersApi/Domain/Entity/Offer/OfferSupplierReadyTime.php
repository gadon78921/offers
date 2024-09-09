<?php

declare(strict_types=1);

namespace App\OffersApi\Domain\Entity\Offer;

use App\OffersApi\Domain\Entity\OfferWithProductQuantity\QuantityFromSupplier;
use App\OffersApi\Domain\Entity\OfferWithProductQuantity\QuantityProduct;
use App\OffersApi\Domain\Entity\TradePoint\TradePointWithSupplierDeliveryTimeRules;
use Doctrine\Common\Collections\ArrayCollection;

final class OfferSupplierReadyTime
{
    /**
     * @param ArrayCollection<int, OfferAmountInStorage> $amountByProduct
     */
    public function __construct(
        public int $supplierId,
        public ?int $readyTime,
        public int $amount,
        public ArrayCollection $amountByProduct,
    ) {}

    public static function create(
        QuantityFromSupplier $product,
        ?TradePointWithSupplierDeliveryTimeRules $tradePointWithSupplierDeliveryTimeRules,
        \DateTimeImmutable $dateTime,
    ): self {
        return new self(
            $product->supplierId,
            $tradePointWithSupplierDeliveryTimeRules?->readyTimeFromTradePointForSupplierId($product->supplierId, $dateTime)?->getTimestamp(),
            $product->totalQuantityFromSupplier(),
            $product->quantity->map(
                fn(QuantityProduct $product) => OfferAmountInStorage::createFromQuantityProduct($product),
            ),
        );
    }
}
