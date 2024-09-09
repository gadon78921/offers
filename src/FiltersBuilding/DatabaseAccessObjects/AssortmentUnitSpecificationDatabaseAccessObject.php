<?php

declare(strict_types=1);

namespace App\FiltersBuilding\DatabaseAccessObjects;

use Doctrine\DBAL\Connection;
use MartinGeorgiev\Utils\DataStructure;

final class AssortmentUnitSpecificationDatabaseAccessObject
{
    public function __construct(
        private readonly Connection $connection,
    ) {}

    /**
     * @param array<int> $assortmentUnitIds
     *
     * @return \Traversable<int, array{'assortmentUnitId': int, 'activeSubstance': string, 'brand': string, 'categoryName': string, 'dosage': string, 'dosageForm': string, 'manufacturerCountryName': string, 'manufacturerName': string, 'packageQuantity': int, 'sellProcedure': string, 'subtitle': string}>
     */
    public function fetch(array $assortmentUnitIds, int $limit = 1000, int $offset = 0): \Traversable
    {
        $sql = <<<SQL
                SELECT aus.assortment_unit_id        as "assortmentUnitId",
                       aus.active_substance          as "activeSubstance",
                       aus.brand                     as "brand",
                       pgp.path->-1->>'title'        as "categoryName",
                       aus.dosage                    as "dosage",
                       aus.dosage_form               as "dosageForm",
                       aus.manufacturer_country_name as "manufacturerCountryName",
                       aus.manufacturer_name         as "manufacturerName",
                       aus.package_quantity          as "packageQuantity",
                       aus.sell_procedure            as "sellProcedure",
                       aus.subtitle                  as "subtitle"
                FROM assortment_unit_specifications aus
                JOIN products_groups_path pgp ON pgp.assortment_unit_id = aus.assortment_unit_id
            SQL;

        $sql .= empty($assortmentUnitIds) ? '' : ' WHERE aus.assortment_unit_id = ANY(:assortmentUnitIds)';
        $sql .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;

        return $this->connection->executeQuery(
            $sql,
            [
                'assortmentUnitIds' => DataStructure::transformPHPArrayToPostgresTextArray($assortmentUnitIds),
            ]
        )->iterateAssociative();
    }
}
