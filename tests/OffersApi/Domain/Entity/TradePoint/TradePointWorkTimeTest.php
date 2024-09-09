<?php

declare(strict_types=1);

namespace App\Tests\OffersApi\Domain\Entity\TradePoint;

use App\OffersApi\Domain\Entity\TradePoint\TradePointWorkTime;
use App\Tests\OffersApi\DataHelperCase;
use PHPUnit\Framework\TestCase;

final class TradePointWorkTimeTest extends TestCase
{
    public function testIsValid(): void
    {
        $tradePointWorkTime = DataHelperCase::getTradePointWorkTime();

        self::assertTrue($tradePointWorkTime->isValid());
    }

    public function testIsValidFalse(): void
    {
        $tradePoint    = 1;
        $workStartHour = 9;
        $workEndHour   = 21;
        $daysOfWork    = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт'];
        $validFrom     = new \DateTimeImmutable('2099-04-01');

        $tradePointWorkTime = new TradePointWorkTime(
            $tradePoint,
            $workStartHour,
            $workEndHour,
            $daysOfWork,
            $validFrom,
        );

        self::assertFalse($tradePointWorkTime->isValid());
    }

    public function testReadyTimeFromTradePoint(): void
    {
        $tradePointWorkTime = DataHelperCase::getTradePointWorkTime();

        $dateTime          = new \DateTimeImmutable('2023-04-04 10:00:00'); // вторник
        $expectedReadyTime = new \DateTimeImmutable('2023-04-04 11:00:00');

        self::assertEquals($expectedReadyTime, $tradePointWorkTime->readyTimeFromTradePoint($dateTime));
    }

    public function testReadyTimeFromTradePointWithWeekendsOnly(): void
    {
        $tradePoint    = 1;
        $workStartHour = 9;
        $workEndHour   = 21;
        $daysOfWork    = ['Сб', 'Вс'];
        $validFrom     = new \DateTimeImmutable('2023-04-01');

        $tradePointWorkTime = new TradePointWorkTime(
            $tradePoint,
            $workStartHour,
            $workEndHour,
            $daysOfWork,
            $validFrom,
        );

        $dateTime          = new \DateTimeImmutable('2023-04-08 10:00:00'); // суббота
        $expectedReadyTime = new \DateTimeImmutable('2023-04-08 11:00:00');

        self::assertEquals($expectedReadyTime, $tradePointWorkTime->readyTimeFromTradePoint($dateTime));
    }

    public function testReadyTimeFromTradePointWithWeekdaysOnly(): void
    {
        $tradePointWorkTime = DataHelperCase::getTradePointWorkTime();

        $dateTime          = new \DateTimeImmutable('2023-04-08 10:00:00'); // суббота, не рабочий день аптеки
        $expectedReadyTime = new \DateTimeImmutable('2023-04-10 09:00:00'); // понедельник - первый рабочий день

        self::assertEquals($expectedReadyTime, $tradePointWorkTime->readyTimeFromTradePoint($dateTime));
    }

    public function testReadyTimeFromTradePointWithWeekdaysOnly2(): void
    {
        $tradePointWorkTime = DataHelperCase::getTradePointWorkTime();

        $dateTime          = new \DateTimeImmutable('2023-04-04 22:00:00'); // вторник, время после закрытия аптеки
        $expectedReadyTime = new \DateTimeImmutable('2023-04-05 09:00:00'); // среда, время открытия аптеки

        self::assertEquals($expectedReadyTime, $tradePointWorkTime->readyTimeFromTradePoint($dateTime));
    }
}
