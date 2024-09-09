<?php

declare(strict_types=1);

namespace App\Tests\OffersApi\Domain\Entity\OfferWithProductQuantity;

use App\Tests\OffersApi\DataHelperCase;
use PHPUnit\Framework\TestCase;

final class OfferWithProductsQuantityTest extends TestCase
{
    public function testOfferWithProductsQuantity(): void
    {
        $sut = DataHelperCase::getOfferWithProductsQuantity();

        self::assertSame($sut->assortmentUnitId, 105733);
        self::assertSame($sut->productsQuantity->count(), 2);
        self::assertSame($sut->productsQuantity->get(1)->tradePointId, 3);
        self::assertSame($sut->productsQuantity->get(1)->quantityFromSuppliers->get(0)->supplierId, 99900000016);
    }
}
