<?php

declare(strict_types=1);

namespace App\Tests\OffersApi\Domain\Entity\TradePoint;

use App\Tests\OffersApi\DataHelperCase;
use PHPUnit\Framework\TestCase;

final class TradePointWithSupplierDeliveryTimeRulesTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testTradePointWithSupplierDeliveryTimeRules(int $supplierId, string $date, string $result): void
    {
        $sut = DataHelperCase::getTradePointWithSupplierDeliveryTimeRules();

        $readyTime = $sut->readyTimeFromTradePointForSupplierId($supplierId, \DateTimeImmutable::createFromFormat('d.m.Y H', $date));

        self::assertSame($readyTime->format('d.m.Y H'), $result);
    }

    /** @return array<int, array<int, string>> */
    public static function dataProvider(): array
    {
        return [
            [1100009191, '05.06.2023 14', '07.06.2023 09'],
            [1100009191, '07.06.2023 10', '12.06.2023 09'],
            [99900000076, '05.06.2023 14', '07.06.2023 16'],
            [99900000076, '07.06.2023 10', '07.06.2023 16'],
        ];
    }
}
