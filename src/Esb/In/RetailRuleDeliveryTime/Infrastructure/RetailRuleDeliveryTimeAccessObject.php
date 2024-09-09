<?php

declare(strict_types=1);

namespace App\Esb\In\RetailRuleDeliveryTime\Infrastructure;

use Doctrine\DBAL\Connection;

/**
 * @codeCoverageIgnore
 */
final class RetailRuleDeliveryTimeAccessObject
{
    private const RETAIL_RULE_DELIVERY_TIME = 'retail_rule_delivery_time';

    public function __construct(
        private readonly Connection $connection
    ) {}

    public function save(RetailRuleDeliveryTimeTransferObject $rule): void
    {
        $values = [
            $rule->headId,
            $rule->ruleId,
            $rule->regionId,
            $rule->supplierId,
            $rule->firmId,
            $rule->isForTZOnly ? 'true' : 'false',
            $rule->dateFrom->format(DATE_ATOM),
        ];

        $sql = '
            INSERT INTO ' . self::RETAIL_RULE_DELIVERY_TIME . ' (
                head_id, rule_id, region_id, supplier_id, firm_id, is_for_tz_only, date_from
            )
            VALUES (:head_id, :rule_id, :region_id, :supplier_id, :firm_id, :is_for_tz_only, :date_from)
            ON CONFLICT (head_id, rule_id, region_id) DO UPDATE SET
                supplier_id = EXCLUDED.supplier_id,
                firm_id = EXCLUDED.firm_id,
                is_for_tz_only = EXCLUDED.is_for_tz_only,
                date_from = EXCLUDED.date_from
         ';

        $this->connection->executeStatement($sql, $values);
    }

    public function remove(int $headId): void
    {
        $sql = 'DELETE FROM ' . self::RETAIL_RULE_DELIVERY_TIME . ' WHERE head_id = :headId';
        $this->connection->executeStatement($sql, ['headId' => $headId]);
    }
}
