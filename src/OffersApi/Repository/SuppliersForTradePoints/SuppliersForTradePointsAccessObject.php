<?php

declare(strict_types=1);

namespace App\OffersApi\Repository\SuppliersForTradePoints;

use Doctrine\DBAL\Connection;
use MartinGeorgiev\Utils\DataStructure;

final class SuppliersForTradePointsAccessObject
{
    public function __construct(
        private readonly Connection $connection,
    ) {}

    /** @return array<int, array{'tradePointId': int, 'supplierIds': array<int>}> */
    public function fetchAllTradePointsWithSupplierIdsFromCity(int $tradePointId): array
    {
        $sql = <<<SQL
                SELECT 
                    trade_point_id    as "tradePointId",
                    supplier_list_ids as "supplierIds"
                FROM retail_tradepoints
                WHERE kladr_id = (SELECT kladr_id FROM retail_tradepoints WHERE trade_point_id = :tradePointId)
            SQL;

        $tradePointsWithSupplierIds = $this->connection->executeQuery($sql, [
            'tradePointId' => $tradePointId,
        ])->fetchAllAssociative();

        return array_map(static function (array $tradePoint) {
            $tradePoint['supplierIds'] = DataStructure::transformPostgresTextArrayToPHPArray($tradePoint['supplierIds'] ?? '');

            return $tradePoint;
        }, $tradePointsWithSupplierIds);
    }
}
