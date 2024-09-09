<?php

declare(strict_types=1);

namespace App\OffersApi\Domain\Entity\TradePoint;

final class TradePointWorkTime
{
    private const NUMBER_OF_DAY_OF_WEEK_MAP = [
        '',
        'Пн',
        'Вт',
        'Ср',
        'Чт',
        'Пт',
        'Сб',
        'Вс',
    ];

    public const DEFAULT_START_HOUR_ASSEMBLY = 9;
    public const DEFAULT_END_HOUR_ASSEMBLY   = 24;

    public function __construct(
        public readonly int $tradePoint,
        public readonly int $workStartHour,
        public readonly int $workEndHour,
        /** @var array<int, string> $daysOfWork */
        public readonly array $daysOfWork,
        public readonly ?\DateTimeImmutable $validFrom,
    ) {}

    public function readyTimeFromTradePoint(?\DateTimeImmutable $dateTime = null): \DateTimeImmutable
    {
        $numbersOfDayOfWeek = empty($this->daysOfWork) ? self::NUMBER_OF_DAY_OF_WEEK_MAP : array_intersect(self::NUMBER_OF_DAY_OF_WEEK_MAP, $this->daysOfWork);

        $dateTime ??= new \DateTimeImmutable();
        $hourNow   = (int) $dateTime->format('H');
        $readyTime = $dateTime->setTime($hourNow, 0);

        if ($hourNow < $this->workStartHour) {
            $readyTime = $readyTime->setTime($this->workStartHour, 0);
        }

        if ($hourNow >= $this->workEndHour) {
            $readyTime = $readyTime->modify('+1 day')->setTime($this->workStartHour, 0);
        }

        for ($numberOfDays = 0; $numberOfDays <= 7; ++$numberOfDays) {
            $newReadyTime = $readyTime->modify('+' . $numberOfDays . ' days');
            $weekDayNow   = (int) $newReadyTime->format('N');

            if ($numberOfDays > 0) {
                $newReadyTime = $newReadyTime->setTime($this->workStartHour, 0);
            }

            if (isset($numbersOfDayOfWeek[$weekDayNow])) {
                $readyTime = $newReadyTime;
                break;
            }
        }

        return $readyTime;
    }

    public function isValid(): bool
    {
        return new \DateTimeImmutable() > $this->validFrom;
    }
}
