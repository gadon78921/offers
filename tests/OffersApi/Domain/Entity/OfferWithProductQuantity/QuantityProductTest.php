<?php

declare(strict_types=1);

namespace App\Tests\OffersApi\Domain\Entity\OfferWithProductQuantity;

use App\Tests\OffersApi\DataHelperCase;
use PHPUnit\Framework\TestCase;

final class QuantityProductTest extends TestCase
{
    public function testQuantityProduct(): void
    {
        $sut = DataHelperCase::getQuantityProudct();

        self::assertSame($sut->retailProductId, 15099);
        self::assertSame($sut->quantity, 10);
        self::assertSame($sut->quantityUnpacked, 4);
    }
}
