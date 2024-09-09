<?php

declare(strict_types=1);

namespace App\OffersBuilding\Repository\OffersFromSuppliers;

use App\OffersBuilding\Domain\Entity\ProductFromSupplier;
use App\OffersBuilding\Domain\Entity\TradePoint;
use Doctrine\Common\Collections\ArrayCollection;

final class ProductFromSupplierHydrator
{
    /** @param array{'kaisProductId': int, 'assortmentUnitId': int, 'productName': string, 'supplierId': int, 'priceFromSupplier': float, 'quantity': int, 'tradePoints': array<int<0, max>, TradePoint>} $productData */
    public function hydrateProductFromSupplier(array $productData): ProductFromSupplier
    {
        return new ProductFromSupplier(
            (int) $productData['kaisProductId'],
            (int) $productData['assortmentUnitId'],
            (string) $productData['productName'],
            (int) $productData['supplierId'],
            (float) $productData['priceFromSupplier'],
            (int) $productData['quantity'],
            new ArrayCollection($productData['tradePoints'])
        );
    }
}
