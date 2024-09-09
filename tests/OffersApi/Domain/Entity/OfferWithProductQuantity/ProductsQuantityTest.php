<?php

declare(strict_types=1);

namespace App\Tests\OffersApi\Domain\Entity\OfferWithProductQuantity;

use App\Tests\OffersApi\DataHelperCase;
use PHPUnit\Framework\TestCase;

final class ProductsQuantityTest extends TestCase
{
    public function testProductQuantity(): void
    {
        $sut = DataHelperCase::getProductsQuantity();

        self::assertSame($sut->tradePointId, 10);

        self::assertSame($sut->quantityInTradePoint->count(), 2);
        self::assertSame($sut->quantityInTradePoint->get(1)->retailProductId, 10029);

        self::assertSame($sut->quantityFromSuppliers->count(), 2);
        self::assertSame($sut->quantityFromSuppliers->get(1)->supplierId, 1100009191);
        self::assertSame($sut->quantityFromSuppliers->get(1)->quantity->get(0)->retailProductId, 8672);
    }
}
