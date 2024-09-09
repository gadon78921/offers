<?php

declare(strict_types=1);

namespace App\Tests\OffersApi\Domain\Entity\TradePoint;

use App\OffersApi\Domain\Entity\TradePoint\SuppliersForTradePoint;
use PHPUnit\Framework\TestCase;

final class SuppliersForTradePointTest extends TestCase
{
    public function testSuppliersForTradePoint(): void
    {
        $sut = new SuppliersForTradePoint(
            4,
            [1100009191, 99900000076],
        );

        self::assertSame($sut->tradePointId, 4);
        self::assertSame($sut->supplierIds, [1100009191, 99900000076]);
    }
}
