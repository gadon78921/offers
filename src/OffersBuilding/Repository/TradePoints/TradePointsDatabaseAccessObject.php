<?php

declare(strict_types=1);

namespace App\OffersBuilding\Repository\TradePoints;

use Doctrine\DBAL\Connection;
use MartinGeorgiev\Utils\DataStructure;

final class TradePointsDatabaseAccessObject
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    /** @return array<int, array{'kladrId': string}> */
    public function fetchKladrIds(): array
    {
        $sql = '
            SELECT DISTINCT kladr_id as "kladrId"
            FROM retail_tradepoints
            WHERE kladr_id IS NOT NULL OR kladr_id != \'NULL\'
        ';

        return $this->connection->executeQuery($sql)->fetchAllAssociative();
    }

    /** @return array{array{'tradePointId': int, 'kladrId': string, 'deliveryAvailable': bool, 'firmIds': array{int}, 'supplierIds': array{int}}} */
    public function fetchTradePoints(): array
    {
        $sql = '
            SELECT trade_point_id     as "tradePointId",
                   kladr_id           as "kladrId",
                   delivery_available as "deliveryAvailable",
                   firm_list_ids      as "firmIds",
                   supplier_list_ids  as "supplierIds"
            FROM retail_tradepoints
            WHERE kladr_id IS NOT NULL OR kladr_id != \'NULL\'
        ';

        $tradePoints = $this->connection->executeQuery($sql)->fetchAllAssociative();

        return array_map(static function (array $tradePoint) {
            $tradePoint['firmIds']     = DataStructure::transformPostgresTextArrayToPHPArray($tradePoint['firmIds'] ?? '');
            $tradePoint['supplierIds'] = DataStructure::transformPostgresTextArrayToPHPArray($tradePoint['supplierIds'] ?? '');

            return $tradePoint;
        }, $tradePoints);
    }
}
