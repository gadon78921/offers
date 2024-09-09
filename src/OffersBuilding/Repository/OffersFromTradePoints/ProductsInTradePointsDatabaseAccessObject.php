<?php

declare(strict_types=1);

namespace App\OffersBuilding\Repository\OffersFromTradePoints;

use Doctrine\DBAL\Connection;
use MartinGeorgiev\Utils\DataStructure;

final class ProductsInTradePointsDatabaseAccessObject
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    /**
     * @param array<int> $firmIds
     * @param array<int> $assortmentUnitIds
     * @param array<int> $kaisProductIds
     *
     * @return \Traversable<int, array{'assortmentUnitId': int, 'kaisProductId': int, 'firmId': string, 'priceInTradePoint': float, 'quantityInTradePoint': int, 'quantityInTradePointUnpacked': int, 'wholesalePrice': float}>
     */
    public function fetchProductsInFirms(array $firmIds, array $assortmentUnitIds = [], array $kaisProductIds = []): \Traversable
    {
        $sql = '
            SELECT rp.assortment_unit_id        as "assortmentUnitId",
                   aus.product_name             as "productName",
                   rs.kais_product_id           as "kaisProductId",
                   rs.firm_subdivision_id       as "firmId",
                   rs.retail_price_with_tax     as "priceInTradePoint",
                   rs.free_qty                  as "quantityInTradePoint",
                   rs.divided_free_qty          as "quantityInTradePointUnpacked",
                   rs.avg_income_price_with_tax as "wholesalePrice"
            FROM retail_stocks rs
            JOIN retail_products rp ON rs.kais_product_id = rp.kais_product_id
            JOIN assortment_unit_specifications aus ON aus.assortment_unit_id = rp.assortment_unit_id
            WHERE rs.firm_subdivision_id = ANY(:firmIds)
            AND (rs.free_qty > 0 OR rs.divided_free_qty > 0)
        ';

        $sql .= empty($assortmentUnitIds) ? '' : ' AND rp.assortment_unit_id = ANY(:assortmentUnitIds)';
        $sql .= empty($kaisProductIds) ? '' : ' AND rs.kais_product_id = ANY(:kaisProductIds)';

        return $this->connection->executeQuery(
            $sql,
            [
                'firmIds'           => DataStructure::transformPHPArrayToPostgresTextArray($firmIds),
                'assortmentUnitIds' => DataStructure::transformPHPArrayToPostgresTextArray($assortmentUnitIds),
                'kaisProductIds'    => DataStructure::transformPHPArrayToPostgresTextArray($kaisProductIds),
            ]
        )->iterateAssociative();
    }
}
