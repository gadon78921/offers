<?php

declare(strict_types=1);

namespace App\OffersBuilding\Gateway;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

abstract class DatabaseAccessObject
{
    protected const ROWS_NUMBER_FOR_QUERY = 1000;

    public function __construct(
        protected readonly Connection $connection,
        protected readonly LoggerInterface $logger,
        protected readonly string $tableName,
    ) {}

    /** @param array<int> $assortmentUnitIds */
    public function removeByAssortmentUnitIds(array $assortmentUnitIds): void
    {
        foreach (array_chunk($assortmentUnitIds, self::ROWS_NUMBER_FOR_QUERY) as $chunk) {
            $sql = 'DELETE FROM ' . $this->tableName . ' WHERE assortment_unit_id IN (' . implode(',', $chunk) . ')';
            $this->connection->executeStatement($sql);
        }
    }

    /** @param array<int> $tradePointIds */
    public function removeByTradePointIds(array $tradePointIds): void
    {
        $sql = 'DELETE FROM ' . $this->tableName . ' WHERE tradepoint_id IN (' . implode(',', $tradePointIds) . ')';
        $this->connection->executeStatement($sql);
    }

    public function removeByKladrId(string $kladrId): void
    {
        $sql = 'DELETE FROM ' . $this->tableName . ' WHERE kladr_id = \'' . $kladrId . '\'';
        $this->connection->executeStatement($sql);
    }

    /** @param array<int> $assortmentUnitIds */
    public function removeByAssortmentUnitIdsAndKladrId(array $assortmentUnitIds, string $kladrId): void
    {
        foreach (array_chunk($assortmentUnitIds, self::ROWS_NUMBER_FOR_QUERY) as $chunk) {
            $sql = 'DELETE FROM ' . $this->tableName . ' WHERE assortment_unit_id IN (' . implode(',', $chunk) . ') AND kladr_id = \'' . $kladrId . '\'';
            $this->connection->executeStatement($sql);
        }
    }

    /** @param array<int> $kaisProductIds */
    public function removeByKaisProductIdsAndKladrId(array $kaisProductIds, string $kladrId): void
    {
        foreach (array_chunk($kaisProductIds, self::ROWS_NUMBER_FOR_QUERY) as $chunk) {
            $sql = '
                DELETE
                FROM ' . $this->tableName . ' target
                USING retail_products rp
                WHERE rp.assortment_unit_id = target.assortment_unit_id
                AND rp.kais_product_id IN (' . implode(',', $chunk) . ')
                AND kladr_id = \'' . $kladrId . '\'
            ';

            $this->connection->executeStatement($sql);
        }
    }

    /**
     * @param array<int> $assortmentUnitIds
     * @param array<int> $tradePointIds
     */
    public function removeByAssortmentUnitIdsAndTradePointIds(array $assortmentUnitIds, array $tradePointIds): void
    {
        foreach (array_chunk($assortmentUnitIds, self::ROWS_NUMBER_FOR_QUERY) as $chunk) {
            $sql = 'DELETE FROM ' . $this->tableName . ' WHERE assortment_unit_id IN (' . implode(',', $chunk) . ') AND tradepoint_id IN (' . implode(',', $tradePointIds) . ')';
            $this->connection->executeStatement($sql);
        }
    }

    /**
     * @param array<int> $kaisProductIds
     * @param array<int> $tradePointIds
     */
    public function removeByKaisProductIdsAndTradePointIds(array $kaisProductIds, array $tradePointIds): void
    {
        foreach (array_chunk($kaisProductIds, self::ROWS_NUMBER_FOR_QUERY) as $chunk) {
            $sql = '
                DELETE
                FROM ' . $this->tableName . ' target
                USING retail_products rp
                WHERE rp.assortment_unit_id = target.assortment_unit_id
                AND rp.kais_product_id IN (' . implode(',', $chunk) . ')
                AND tradepoint_id IN (' . implode(',', $tradePointIds) . ')
            ';

            $this->connection->executeStatement($sql);
        }
    }

    /** @param array<string> $rows */
    abstract public function insert(array $rows): void;
}
