<?php

declare(strict_types=1);

namespace App\OffersBuilding\Repository\RuleSupplierToTradePoint;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;

final class RuleSupplierToTradePointAccessObject
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    /**
     * @param array{int} $supplierIds
     *
     * @return array<int, array{'supplier_id': int, 'trade_point_id': int|null, 'isForTzOnlyValues': string}>
     */
    public function getRulesBySupplierIds(array $supplierIds, string $kladrId): array
    {
        $sql = <<<SQL
                SELECT rrdt.supplier_id, rt.trade_point_id,
                       jsonb_agg(
                           json_build_object(
                               'isForTzOnly', rrdt.is_for_tz_only
                           )
                           ORDER BY rrdt.date_from DESC
                       ) as "isForTzOnlyValues"
                FROM retail_rule_delivery_time rrdt
                JOIN retail_tradepoints rt on rrdt.firm_id::varchar = ANY(rt.firm_list_ids)
                WHERE rrdt.supplier_id IN (:supplierIds)
                AND rrdt.region_id = :regionId
                AND date_from <= now()
                GROUP BY rrdt.supplier_id, rt.trade_point_id
            SQL;

        return $this->connection->executeQuery(
            $sql,
            [
                'supplierIds' => $supplierIds,
                'regionId'    => substr($kladrId, 0, 2),
            ],
            [
                'supplierIds' => ArrayParameterType::INTEGER,
            ],
        )->fetchAllAssociative();
    }
}
