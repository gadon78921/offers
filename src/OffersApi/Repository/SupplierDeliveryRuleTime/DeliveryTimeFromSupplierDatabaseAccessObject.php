<?php

declare(strict_types=1);

namespace App\OffersApi\Repository\SupplierDeliveryRuleTime;

use Doctrine\DBAL\Connection;

final class DeliveryTimeFromSupplierDatabaseAccessObject
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    /**
     * @return \Traversable<int, array{'supplierId': int, 'firmId': int, 'rules': string}>
     */
    public function fetch(string $kladrId): \Traversable
    {
        $regionId = substr($kladrId, 0, 2);

        $sql = <<<SQL
                SELECT
                    rdtfs.supplier_id as "supplierId",
                    rdtfs.firm_id     as "firmId",
                    jsonb_agg(
                        json_build_object(
                            'firmId', rdtfs.firm_id,
                            'daysToSendOrders', rdtfs.days_to_send_orders,
                            'orderSendTime', rdtfs.order_send_time,
                            'hoursUntilReady', rdtfs.hours_until_ready
                        )
                    ) as "rules"
                FROM retail_delivery_time_from_suppliers rdtfs
                WHERE rdtfs.region_id = :regionId
                AND (
                        rdtfs.firm_id::varchar IN (
                            SELECT unnest(firm_list_ids) FROM retail_tradepoints WHERE kladr_id = :kladrId
                        )
                        OR rdtfs.firm_id = 0
                        OR rdtfs.firm_id IS NULL
                )
                GROUP BY rdtfs.firm_id, rdtfs.supplier_id
            SQL;

        return $this->connection->executeQuery(
            $sql,
            [
                'kladrId'  => $kladrId,
                'regionId' => $regionId,
            ]
        )->iterateAssociative();
    }

    /**
     * @return array<int, int>
     */
    public function fetchTradePointIndexedByFirmId(string $kladrId): array
    {
        $sql = <<<SQL
                SELECT unnest(firm_list_ids) as "firmId",
                       trade_point_id        as "tradePointId"
                FROM retail_tradepoints
                WHERE kladr_id = :kladrId
            SQL;

        return $this->connection->executeQuery($sql, ['kladrId' => $kladrId])->fetchAllKeyValue();
    }
}
