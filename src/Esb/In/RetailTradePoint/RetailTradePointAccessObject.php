<?php

declare(strict_types=1);

namespace App\Esb\In\RetailTradePoint;

use Doctrine\DBAL\Connection;
use MartinGeorgiev\Utils\DataStructure;

final class RetailTradePointAccessObject
{
    private const RETAIL_TRADEPOINTS_TABLE = 'retail_tradepoints';

    public function __construct(
        private readonly Connection $connection
    ) {}

    /** @return array{'kladr_id': string, array{int}} */
    public function getTradePointIdsBySupplierId(string $supplierId): array
    {
        $sql = '
            SELECT kladr_id, array_agg(trade_point_id)
            FROM ' . self::RETAIL_TRADEPOINTS_TABLE . '
            WHERE :supplierId = ANY(supplier_list_ids)
            GROUP BY kladr_id
        ';

        $result = $this->connection->executeQuery($sql, ['supplierId' => $supplierId])->fetchAllKeyValue();
        array_walk($result, static fn(&$row) => $row = DataStructure::transformPostgresTextArrayToPHPArray($row));

        return $result;
    }

    /** @return array{int} */
    public function getTradePointIdsByFirmId(string $firmId): array
    {
        $sql = 'SELECT trade_point_id FROM ' . self::RETAIL_TRADEPOINTS_TABLE . ' WHERE :firmId = ANY(firm_list_ids)';

        return $this->connection->executeQuery($sql, ['firmId' => $firmId])->fetchFirstColumn();
    }
}
