<?php

declare(strict_types=1);

namespace App\Tests\OffersApi\Domain\Entity\TradePoint;

use App\OffersApi\Domain\Entity\TradePoint\SupplierDeliveryTimeRules;
use PHPUnit\Framework\TestCase;

final class SupplierDeliveryTimeRulesTest extends TestCase
{
    public function testSupplierDeliveryTimeRules(): void
    {
        $data = [
            ['daysToSendOrders' => ['Пн'], 'orderSendTime' => '10:00:00', 'hoursUntilReady' => 3, 'firmId' => 0],
            ['daysToSendOrders' => ['Вт', 'Пт'], 'orderSendTime' => '20:00:00', 'hoursUntilReady' => 10, 'firmId' => 0],
        ];

        $sut = SupplierDeliveryTimeRules::create(123, $data);

        self::assertSame($sut->supplierId, 123);
        self::assertSame($sut->rules->count(), 2);
        self::assertSame($sut->rules->get(0)->daysToSendOrders, ['Пн']);
        self::assertSame($sut->rules->get(0)->orderSendHour, 10);
        self::assertSame($sut->rules->get(0)->hoursUntilReady, 3);
        self::assertSame($sut->rules->get(1)->daysToSendOrders, ['Вт', 'Пт']);
        self::assertSame($sut->rules->get(1)->orderSendHour, 20);
        self::assertSame($sut->rules->get(1)->hoursUntilReady, 10);
    }
}
