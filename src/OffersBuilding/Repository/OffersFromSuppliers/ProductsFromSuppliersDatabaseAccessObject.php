<?php

declare(strict_types=1);

namespace App\OffersBuilding\Repository\OffersFromSuppliers;

use Doctrine\DBAL\Connection;
use MartinGeorgiev\Utils\DataStructure;

final class ProductsFromSuppliersDatabaseAccessObject
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    /**
     * @param array<int> $suppliersIds
     * @param array<int> $assortmentUnitIds
     * @param array<int> $kaisProductIds
     *
     * @return \Traversable<int, array{'assortmentUnitId': int, 'kaisProductId': int, 'supplierId': int, 'priceFromSupplier': float, 'quantity': int}>
     */
    public function fetchProductsFromSuppliers(array $suppliersIds, array $assortmentUnitIds = [], array $kaisProductIds = []): \Traversable
    {
        $sql = '
            SELECT rp.assortment_unit_id as "assortmentUnitId",
                   aus.product_name      as "productName",
                   rsp.kais_product_id   as "kaisProductId",
                   rsp.supplier_id       as "supplierId",
                   rsp.supplier_price    as "priceFromSupplier",
                   rsp.quantity          as "quantity"
            FROM retail_supplier_prices rsp
            JOIN retail_products rp on rsp.kais_product_id = rp.kais_product_id
            JOIN assortment_unit_specifications aus ON aus.assortment_unit_id = rp.assortment_unit_id
            WHERE rsp.supplier_id = ANY(:supplierIds)
            AND rsp.quantity > 0
        ';

        $sql .= empty($assortmentUnitIds) ? '' : ' AND rp.assortment_unit_id = ANY(:assortmentUnitIds)';
        $sql .= empty($kaisProductIds) ? '' : ' AND rsp.kais_product_id = ANY(:kaisProductIds)';

        return $this->connection->executeQuery(
            $sql,
            [
                'supplierIds'       => DataStructure::transformPHPArrayToPostgresTextArray($suppliersIds),
                'assortmentUnitIds' => DataStructure::transformPHPArrayToPostgresTextArray($assortmentUnitIds),
                'kaisProductIds'    => DataStructure::transformPHPArrayToPostgresTextArray($kaisProductIds),
            ]
        )->iterateAssociative();
    }
}
