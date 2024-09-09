<?php

declare(strict_types=1);

namespace App\OffersApi\Repository\Products;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final class ProductsInTradePointsDatabaseAccessObject
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    /**
     * @return list<array{'id': int, 'qty': int}>
     *
     * @throws Exception
     */
    public function fetchProductsStocks(int $tradePointId, ?string $lastSyncDateTime): array
    {
        $tradePointIds = $this->connection->executeQuery(
            'SELECT tradepoint_id FROM yandex_tradepoints yt WHERE yt.tradepoint_id = :tradePointId',
            [
                'tradePointId' => $tradePointId,
            ]
        )->rowCount();

        if (0 === $tradePointIds) {
            return [];
        }

        $andWhereForProducts = '';
        $andWhereForStock    = '';

        if (null !== $lastSyncDateTime) {
            $andWhereForProducts = ' OR kp.updated_at > :lastSyncDateTime ';
            $andWhereForStock    = ' AND rs.updated_at > :lastSyncDateTime ';
        }
        $sql = "
            SELECT 
                rs.kais_product_id AS id,
                CASE WHEN kp.not_for_yandex_eda = true THEN 0 ELSE SUM(rs.free_qty) END as qty
            FROM retail_stocks rs
            LEFT JOIN yandex_products yp on rs.kais_product_id = yp.kais_product_id 
            JOIN kais_products kp on rs.kais_product_id = kp.kais_product_id 
            JOIN products p ON p.kais_product_id = rs.kais_product_id 
            JOIN retail_tradepoints rt ON rs.firm_subdivision_id = ANY(rt.firm_list_ids) AND rt.trade_point_id = :tradePointId
            WHERE p.tradepoint_id = :tradePointId AND (yp.kais_product_id IS NOT NULL $andWhereForProducts)
            $andWhereForStock GROUP BY rs.kais_product_id, kp.not_for_yandex_eda;
        ";

        return $this->connection->executeQuery(
            $sql,
            [
                'tradePointId'     => $tradePointId,
                'lastSyncDateTime' => $lastSyncDateTime,
            ]
        )->fetchAllAssociative();
    }
}
