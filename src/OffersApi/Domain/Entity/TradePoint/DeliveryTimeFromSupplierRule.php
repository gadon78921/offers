<?php

declare(strict_types=1);

namespace App\OffersApi\Domain\Entity\TradePoint;

final class DeliveryTimeFromSupplierRule
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

    public function __construct(
        /** @var array<int, string> $daysToSendOrders */
        public array $daysToSendOrders,
        public int $orderSendHour,
        public int $hoursUntilReady,
        public int $ruleForFirmId,
    ) {}

    public function readyTimeFromSupplier(?\DateTimeImmutable $readyTime = null): \DateTimeImmutable
    {
        $numbersOfDayOfWeek = array_intersect(self::NUMBER_OF_DAY_OF_WEEK_MAP, $this->daysToSendOrders);

        $readyTime ??= new \DateTimeImmutable();
        $hourNow = (int) $readyTime->format('H');

        if ($hourNow >= $this->orderSendHour) {
            $readyTime = $readyTime->modify('+1 days');
        }

        for ($numberOfDays = 0; $numberOfDays <= 7; ++$numberOfDays) {
            $newReadyTime = $readyTime->modify('+' . $numberOfDays . ' days');
            $weekDayNow   = (int) $newReadyTime->format('N');

            if (isset($numbersOfDayOfWeek[$weekDayNow])) {
                $readyTime = $newReadyTime;
                break;
            }
        }

        $readyTime = $readyTime->setTime($this->orderSendHour, 0);

        return $readyTime->modify('+' . $this->hoursUntilReady . ' hours');
    }
}
