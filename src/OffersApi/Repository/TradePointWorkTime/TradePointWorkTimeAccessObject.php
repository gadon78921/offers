<?php

declare(strict_types=1);

namespace App\OffersApi\Repository\TradePointWorkTime;

use Doctrine\DBAL\Connection;
use MartinGeorgiev\Utils\DataStructure;

final class TradePointWorkTimeAccessObject
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    /** @return array<int, array{'tradePointId': int, 'workStartHour': int, 'workEndHour': int, 'daysOfWork': array<int, string>, 'validFrom': string|null}> */
    public function fetchTradePointsWorkTime(): array
    {
        $sql = <<<SQL
                SELECT rt.trade_point_id   as "tradePointId",
                       rtp.work_start_hour as "workStartHour",
                       rtp.work_end_hour   as "workEndHour",
                       rtp.days_of_work    as "daysOfWork",
                       rtp.date_from       as "validFrom"
                FROM retail_tradepoints rt
                LEFT JOIN retail_rule_tradepoint_params rtp ON rtp.trade_point_id = rt.trade_point_id
            SQL;

        $tradePointsWorkTime = $this->connection->executeQuery($sql)->fetchAllAssociative();

        return array_map(static function (array $tradePointWorkTime) {
            $tradePointWorkTime['daysOfWork'] = DataStructure::transformPostgresTextArrayToPHPArray($tradePointWorkTime['daysOfWork'] ?? '');

            return $tradePointWorkTime;
        }, $tradePointsWorkTime);
    }
}
