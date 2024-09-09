<?php

declare(strict_types=1);

namespace App\Esb\In\RetailRuleTradePointParams\Infrastructure;

use Doctrine\DBAL\Connection;

/**
 * @codeCoverageIgnore
 */
final class RetailRuleDataTradePointParamsAccessObject
{
    private const RETAIL_RULE_TRADEPOINT_PARAMS = 'retail_rule_tradepoint_params';

    public function __construct(
        private readonly Connection $connection
    ) {}

    public function save(RetailRuleDataTradePointParamsTransferObject $rule): void
    {
        $values = [
            $rule->headId,
            $rule->ruleId,
            $rule->tradePointId,
            $rule->workStartHour,
            $rule->workEndHour,
            $rule->daysOfWork,
            $rule->dateFrom->format(DATE_ATOM),
        ];

        $sql = '
            INSERT INTO ' . self::RETAIL_RULE_TRADEPOINT_PARAMS . ' (
                head_id, rule_id, trade_point_id, work_start_hour, work_end_hour, days_of_work, date_from
            )
            VALUES (:head_id, :rule_id, :trade_point_id, :work_start_hour, :work_end_hour, :days_of_work, :date_from)
            ON CONFLICT (head_id, rule_id, trade_point_id) DO UPDATE SET
                trade_point_id  = EXCLUDED.trade_point_id,
                work_start_hour = EXCLUDED.work_start_hour,
                work_end_hour   = EXCLUDED.work_end_hour,
                days_of_work    = EXCLUDED.days_of_work,
                date_from       = EXCLUDED.date_from
         ';

        $this->connection->executeStatement($sql, $values);
    }

    public function remove(int $headId): void
    {
        $sql = 'DELETE FROM ' . self::RETAIL_RULE_TRADEPOINT_PARAMS . ' WHERE head_id = :headId';
        $this->connection->executeStatement($sql, ['headId' => $headId]);
    }
}
