<?php

declare(strict_types=1);

namespace App\Esb\In\RetailRuleCustomerOrderReadyTime\Infrastructure;

use Doctrine\DBAL\Connection;

/**
 * @codeCoverageIgnore
 */
final class RetailDeliveryTimeFromSuppliersAccessObject
{
    private const RETAIL_DELIVERY_TIME_FROM_SUPPLIERS = 'retail_delivery_time_from_suppliers';

    public function __construct(
        private readonly Connection $connection
    ) {}

    public function save(RetailDeliveryTimeFromSuppliersTransferObject $rule): void
    {
        $values = [
            $rule->headId,
            $rule->ruleId,
            $rule->regionId,
            $rule->supplierId,
            $rule->firmId,
            $rule->orderSendTime->format('H:i:s'),
            $rule->hoursUntilReady,
            $rule->dateFrom->format(DATE_ATOM),
        ];

        $sql = '
            INSERT INTO ' . self::RETAIL_DELIVERY_TIME_FROM_SUPPLIERS . ' (
                head_id, rule_id, region_id, supplier_id, firm_id, order_send_time, hours_until_ready, date_from, days_to_send_orders
            )
            VALUES (:head_id, :ruleId, :regionId, :supplierId, :firmId, :orderSendTime, :hoursUntilReady, :dateFrom, ARRAY [\'' . implode('\',\'', $rule->daysToSendOrders) . '\'])
            ON CONFLICT (head_id, rule_id, region_id) DO UPDATE SET
                supplier_id = EXCLUDED.supplier_id,
                firm_id = EXCLUDED.firm_id,
                days_to_send_orders = EXCLUDED.days_to_send_orders,
                order_send_time = EXCLUDED.order_send_time,
                hours_until_ready = EXCLUDED.hours_until_ready,
                date_from = EXCLUDED.date_from
         ';

        $this->connection->executeStatement($sql, $values);
    }

    public function remove(int $headId): void
    {
        $sql = 'DELETE FROM ' . self::RETAIL_DELIVERY_TIME_FROM_SUPPLIERS . ' WHERE head_id = :headId';
        $this->connection->executeStatement($sql, ['headId' => $headId]);
    }
}
