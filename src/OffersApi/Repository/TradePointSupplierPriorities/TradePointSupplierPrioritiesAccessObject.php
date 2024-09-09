<?php

declare(strict_types=1);

namespace App\OffersApi\Repository\TradePointSupplierPriorities;

use Doctrine\DBAL\Connection;
use MartinGeorgiev\Utils\DataStructure;

final class TradePointSupplierPrioritiesAccessObject
{
    public function __construct(
        private readonly Connection $connection,
    ) {}

    /**
     * @param array<int> $assortmentUnitIds
     *
     * @return \Traversable<int, array{'assortmentUnitId': int, 'priorities': string}>
     */
    public function fetch(string $kladrId, array $assortmentUnitIds): \Traversable
    {
        $sql = <<<SQL
                SELECT
                    rp.assortment_unit_id as "assortmentUnitId",
                    jsonb_agg(
                        json_build_object(
                            'tradePointId',    rsp.trade_point_id,
                            'supplierListIds', rsp.supplier_list_ids
                        )
                    ) as "priorities"
                FROM retail_supplier_priorities rsp
                JOIN retail_products rp ON rsp.retail_product_id = rp.retail_product_id
                WHERE rsp.region_id = :regionId
                AND rp.assortment_unit_id = ANY(:assortmentUnitIds)
                GROUP BY rp.assortment_unit_id
            SQL;

        return $this->connection->executeQuery(
            $sql,
            [
                'regionId'          => substr($kladrId, 0, 2),
                'assortmentUnitIds' => DataStructure::transformPHPArrayToPostgresTextArray($assortmentUnitIds),
            ]
        )->iterateAssociative();
    }
}
