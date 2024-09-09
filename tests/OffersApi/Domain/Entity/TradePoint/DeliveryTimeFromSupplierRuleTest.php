<?php

declare(strict_types=1);

namespace App\Tests\OffersApi\Domain\Entity\TradePoint;

use App\OffersApi\Domain\Entity\TradePoint\DeliveryTimeFromSupplierRule;
use PHPUnit\Framework\TestCase;

final class DeliveryTimeFromSupplierRuleTest extends TestCase
{
    private const DATE_FORMAT = 'd.m.Y H';

    /**
     * @dataProvider dataProvider
     *
     * @param array<int, string> $daysToSendOrder
     */
    public function testDeliveryTimeFromSupplierRule(
        array $daysToSendOrder,
        int $orderSendHour,
        int $hoursUntilReady,
        string $date,
        string $result,
    ): void {
        $sut = new DeliveryTimeFromSupplierRule($daysToSendOrder, $orderSendHour, $hoursUntilReady, 0);

        $date = \DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $date);

        self::assertSame($sut->readyTimeFromSupplier($date)->format(self::DATE_FORMAT), $result);
    }

    /** @return array<int, array<string>> */
    public static function dataProvider(): array
    {
        return [
            [['Вт'], 15, 1, '06.06.2023 14', '06.06.2023 16'],
            [['Вт'], 13, 1, '05.06.2023 12', '06.06.2023 14'],
            [['Вт'], 13, 2, '07.06.2023 12', '13.06.2023 15'],
            [['Вт', 'Чт'], 13, 2, '07.06.2023 12', '08.06.2023 15'],
        ];
    }
}
