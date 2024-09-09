<?php

declare(strict_types=1);

namespace App\Tests\OffersApi\Domain\Entity\Offer;

use App\Tests\OffersApi\DataHelperCase;
use PHPUnit\Framework\TestCase;

final class AssortmentUnitAvailabilityTest extends TestCase
{
    public function testAssortmentUnitAvailability(): void
    {
        $sut = DataHelperCase::getAssortmentUnitAvailability();

        self::assertSame($sut->tradePointId, 10);
        self::assertSame($sut->quantityInTradePoint, 25);
        self::assertSame($sut->quantityInTradePointUnpacked, 10);
        self::assertSame($sut->quantityFromSuppliers, 5);
    }
}
