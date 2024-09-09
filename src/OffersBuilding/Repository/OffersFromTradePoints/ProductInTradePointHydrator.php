<?php

declare(strict_types=1);

namespace App\OffersBuilding\Repository\OffersFromTradePoints;

use App\OffersBuilding\Domain\Entity\ProductInTradePoint;
use App\OffersBuilding\Domain\Entity\TradePoint;

final class ProductInTradePointHydrator
{
    /** @param array{'kaisProductId': int, 'assortmentUnitId': int, 'productName': string, 'tradePoint': TradePoint, 'priceInTradePoint': float, 'wholesalePrice': float, 'quantityInTradePoint': int, 'quantityInTradePointUnpacked': int} $productData */
    public function hydrateProductInTradePoint(array $productData): ProductInTradePoint
    {
        return new ProductInTradePoint(
            (int) $productData['kaisProductId'],
            (int) $productData['assortmentUnitId'],
            (string) $productData['productName'],
            $productData['tradePoint'],
            (float) $productData['priceInTradePoint'],
            (float) $productData['wholesalePrice'],
            (int) $productData['quantityInTradePoint'],
            (int) $productData['quantityInTradePointUnpacked']
        );
    }
}
