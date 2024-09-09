<?php

declare(strict_types=1);

namespace App\FiltersBuilding\DatabaseAccessObjects;

use Doctrine\DBAL\Connection;

final class FilterDatabaseAccessObject
{
    public function __construct(
        private readonly Connection $connection,
    ) {}

    /**
     * @param array<int, array{string: string}> $filtersByAssortmentUnitIds
     */
    public function saveBulk(array $filtersByAssortmentUnitIds): void
    {
        $sql = <<<SQL
                INSERT INTO filters
                VALUES {$this->prepareValuesToInsert($filtersByAssortmentUnitIds)}
                ON CONFLICT (assortment_unit_id) DO UPDATE SET
                properties = EXCLUDED.properties
            SQL;

        $this->connection->executeStatement($sql);
    }

    /**
     * @param array<int, array{string: string}> $filtersByAssortmentUnitIds
     */
    private function prepareValuesToInsert(array $filtersByAssortmentUnitIds): string
    {
        foreach ($filtersByAssortmentUnitIds as $assortmentUnitId => $filters) {
            $filtersJson = json_encode($filters, JSON_THROW_ON_ERROR);
            $filtersJson = str_replace('\'', '\'\'', $filtersJson);
            $values[]    = '(' . $assortmentUnitId . ',\'' . $filtersJson . '\')';
        }

        return implode(',', $values ?? []);
    }
}
