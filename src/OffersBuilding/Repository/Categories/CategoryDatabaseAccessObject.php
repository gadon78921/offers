<?php

declare(strict_types=1);

namespace App\OffersBuilding\Repository\Categories;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use MartinGeorgiev\Utils\DataStructure;

final class CategoryDatabaseAccessObject
{
    public function __construct(
        private readonly Connection $connection,
    ) {}

    /**
     * @param array<int> $assortmentUnitIds
     *
     * @return ArrayCollection<int, array<int>>
     */
    public function fetchAssortmentUnitIdsWithCategories(array $assortmentUnitIds = []): ArrayCollection
    {
        $sql = <<<SQL
                SELECT assortment_unit_id as "assortmentUnitId", array_agg(json->>'id') as "categoryIds"
                FROM products_groups_path, jsonb_array_elements(products_groups_path.path) as json
            SQL;

        $sql .= empty($assortmentUnitIds) ? '' : ' WHERE assortment_unit_id IN (:assortmentUnitIds)';
        $sql .= ' GROUP BY assortment_unit_id';

        $assortmentUnitIdsWithCategories = $this->connection->executeQuery($sql, ['assortmentUnitIds' => $assortmentUnitIds], ['assortmentUnitIds' => ArrayParameterType::INTEGER])->fetchAllAssociative();

        $collection = new ArrayCollection();

        array_map(static function (array $assortmentUnitIdWithCategories) use ($collection) {
            $collection->set($assortmentUnitIdWithCategories['assortmentUnitId'], DataStructure::transformPostgresTextArrayToPHPArray($assortmentUnitIdWithCategories['categoryIds'] ?? ''));
        }, $assortmentUnitIdsWithCategories);

        return $collection;
    }
}
