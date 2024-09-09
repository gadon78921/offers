<?php

declare(strict_types=1);

namespace App\OffersApi\Repository\Products;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;

final class ProductsDatabaseAccessObject
{
    public function __construct(
        private readonly Connection $connection,
    ) {}

    /**
     * @param array<int> $assortmentUnitIds
     *
     * @return \Traversable<int, array{'assortmentUnitId': int, 'productsInTradePoints': string, 'productsFromSupplier': string}>
     */
    public function fetch(string $kladrId, array $assortmentUnitIds): \Traversable
    {
        $sql = <<<SQL
                WITH retailProducts as (
                    SELECT kais_product_id, retail_product_id FROM retail_products WHERE assortment_unit_id IN (:assortmentUnitIds)
                )
                SELECT 
                    p.assortment_unit_id as "assortmentUnitId",
                    jsonb_agg(
                        DISTINCT jsonb_build_object(
                            'tradePointId',              p.tradepoint_id,
                            'retailProductId',           rp.retail_product_id,
                            'quantityInStorage',         p.quantity_in_storage,
                            'quantityInStorageUnpacked', p.quantity_in_storage_unpacked
                        )
                    ) as "productsInTradePoints",
                    jsonb_agg(
                        DISTINCT jsonb_build_object(
                            'supplierId',           rs.supplier_id,
                            'retailProductId',      rp.retail_product_id,
                            'quantityFromSupplier', rs.quantity,
                            'cost',                 rs.cost
                        )
                    ) as "productsFromSupplier"
                FROM products p
                JOIN retailProducts rp on p.kais_product_id = rp.kais_product_id
                LEFT JOIN retail_supplier_prices rs on p.kais_product_id = rs.kais_product_id AND rs.supplier_id = ANY(p.supplier_ids)
                WHERE p.kladr_id = :kladrId
                GROUP BY p.assortment_unit_id
            SQL;

        return $this->connection->executeQuery(
            $sql,
            [
                'kladrId'           => $kladrId,
                'assortmentUnitIds' => $assortmentUnitIds,
            ],
            [
                'assortmentUnitIds' => ArrayParameterType::INTEGER,
            ]
        )->iterateAssociative();
    }
}
