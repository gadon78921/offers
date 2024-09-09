<?php

declare(strict_types=1);

namespace App\OffersApi\Repository\TradePointWorkTime;

use App\OffersApi\Domain\Entity\TradePoint\TradePointWorkTime;
use Doctrine\Common\Collections\ArrayCollection;

final class TradePointWorkTimeRepository
{
    /** @var ArrayCollection<int, TradePointWorkTime>|null */
    private ?ArrayCollection $collection = null;

    public function __construct(
        private readonly TradePointWorkTimeAccessObject $dao,
    ) {}

    public function get(int $tradePointId): ?TradePointWorkTime
    {
        if (null === $this->collection) {
            $this->fill();
        }

        return $this->collection->get($tradePointId);
    }

    private function fill(): void
    {
        $this->collection = new ArrayCollection();
        foreach ($this->dao->fetchTradePointsWorkTime() as $raw) {
            $this->collection->set($raw['tradePointId'], $this->hydrate($raw));
        }
    }

    /** @param array{'tradePointId': int, 'workStartHour': int, 'workEndHour': int, 'daysOfWork': array<int, string>, 'validFrom': string|null} $raw */
    private function hydrate(array $raw): TradePointWorkTime
    {
        return new TradePointWorkTime(
            $raw['tradePointId'],
            $raw['workStartHour'] ?? TradePointWorkTime::DEFAULT_START_HOUR_ASSEMBLY,
            $raw['workEndHour'] ?? TradePointWorkTime::DEFAULT_END_HOUR_ASSEMBLY,
            $raw['daysOfWork'],
            null === $raw['validFrom'] ? null : new \DateTimeImmutable($raw['validFrom']),
        );
    }
}
