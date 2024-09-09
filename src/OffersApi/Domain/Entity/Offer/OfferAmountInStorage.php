<?php

declare(strict_types=1);

namespace App\OffersApi\Domain\Entity\Offer;

use App\OffersApi\Domain\Entity\OfferWithProductQuantity\QuantityProduct;

final class OfferAmountInStorage
{
    public function __construct(
        public int $productId,
        public int $amount,
        public int $amountUnpacked,
    ) {}

    public static function createFromQuantityProduct(QuantityProduct $product): self
    {
        return new self(
            $product->retailProductId,
            $product->quantity,
            $product->quantityUnpacked,
        );
    }
}
