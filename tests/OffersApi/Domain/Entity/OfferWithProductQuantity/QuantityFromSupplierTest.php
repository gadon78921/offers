<?php

declare(strict_types=1);

namespace App\Tests\OffersApi\Domain\Entity\OfferWithProductQuantity;

use App\Tests\OffersApi\DataHelperCase;
use PHPUnit\Framework\TestCase;

final class QuantityFromSupplierTest extends TestCase
{
    public function testQuantityFromSupplier(): void
    {
        $sut = DataHelperCase::getQuantityFromSupplier();

        self::assertSame($sut->supplierId, 1100270821);
        self::assertSame($sut->quantity->get(0)->retailProductId, 42561);
        self::assertSame($sut->quantity->get(1)->retailProductId, 28358);
        self::assertSame($sut->quantity->get(2)->retailProductId, 30389);
        self::assertSame($sut->totalQuantityFromSupplier(), 17);
    }
}
