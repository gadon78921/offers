<?php

declare(strict_types=1);

namespace App\ProductsApi\Repository;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;

final class KaisProductsDatabaseAccessObject
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    /**
     * @param list<int> $retailProductIds
     *
     * @return list<array{
     *     'retailProductId'        : int,
     *     'retailProductCode'      : int,
     *     'assortmentUnitId'       : int,
     *     'kaisProductId'          : int,
     *     'baseProductIdInUrl'     : int,
     *     'productName'            : string|null,
     *     'tradeName'              : string|null,
     *     'manufacturerName'       : string|null,
     *     'manufacturerCountryName': string|null,
     *     'storageConditions'      : string|null,
     *     'sellProcedure'          : string|null,
     *     'isVital'                : bool,
     *     'indicatorForUse'        : string|null,
     *     'contraindicationsForUse': string|null,
     *     'pharmodynamic'          : string|null,
     *     'pharmokinetic'          : string|null,
     *     'sideEffect'             : string|null,
     *     'methodOfUse'            : string|null,
     *     'composition'            : string|null,
     *     'fullDescription'        : string|null,
     *     'expirationDate'         : int|null,
     *     'packageName'            : string|null,
     *     'packageQuantity'        : int|null,
     *     'dosage'                 : string|null,
     *     'dosageForm'             : string|null,
     *     'activeSubstance'        : string|null,
     *     'productDescription'     : string|null,
     *     'groupPath'              : string|null,
     *     'fullName'               : string|null,
     *     'isStorageTypeCold'      : int,
     *     'prescriptionForm'       : int
     * }>
     */
    public function getListByRetailProductIds(array $retailProductIds): array
    {
        $sql = <<<SQL
                SELECT
                    rp.retail_product_id               as "retailProductId",
                    rp.retail_product_code             as "retailProductCode",
                    rp.assortment_unit_id              as "assortmentUnitId",
                    p.kais_product_id                  as "kaisProductId",
                    aus.base_product_id                as "baseProductIdInUrl",
                    p.name                             as "productName",
                    p.full_trade_name                  as "tradeName",
                    pr.name                            as "manufacturerName",
                    c.name                             as "manufacturerCountryName",
                    p.storage_conditions               as "storageConditions",
                    p.sell_procedure                   as "sellProcedure",
                    p.is_gnvls                         as "isVital",
                    p.indications                      as "indicatorForUse",
                    p.contraindications                as "contraindicationsForUse",
                    p.pharmacodynamics                 as "pharmodynamic",
                    p.pharmacokinetics                 as "pharmokinetic",
                    p.side_effects                     as "sideEffect",
                    p.dosage_and_administration        as "methodOfUse",
                    p.ingredients                      as "composition",
                    p.description                      as "fullDescription",
                    p.expiration_date                  as "expirationDate",
                    p.full_first_wrapping_name         as "packageName",
                    p.first_packing * p.second_packing as "packageQuantity",
                    p.dose                             as "dosage",
                    sdf.name                           as "dosageForm",
                    g.name                             as "activeSubstance",
                    description."productDescription"   as "productDescription",
                    pgp.path                           as "groupPath",
                    CONCAT(p.full_trade_name, ' ', description."productDescription", ' ', p.packing)   as "fullName",
                    CASE WHEN p.storage_type IN ('ОП', 'П', 'СП', 'Х', 'ЗХ') THEN 1 ELSE 0 END as "isStorageTypeCold",
                    CASE
                        WHEN p.actual_sell_procedure = 'По форме рецептурного бланка 148-1/у-88' THEN 148
                        WHEN p.actual_sell_procedure = 'По форме рецептурного бланка 107-1/у' THEN 107
                        ELSE 0
                    END as "prescriptionForm"
                FROM kais_products p
                JOIN retail_products rp ON rp.kais_product_id = p.kais_product_id
                JOIN kais_producers pr ON pr.id = p.producer_id
                JOIN kais_countries c ON c.id = pr.kais_country_id
                LEFT JOIN kais_simplified_dosage_form sdf ON sdf.id = p.simplified_dosage_form_id
                LEFT JOIN kais_generic g ON g.id = p.generic_id
                LEFT JOIN assortment_unit_specifications aus ON aus.assortment_unit_id = rp.assortment_unit_id
                LEFT JOIN products_groups_path pgp ON pgp.assortment_unit_id = rp.assortment_unit_id
                LEFT JOIN LATERAL (
                    SELECT CONCAT_WS(
                                   ' ',
                                   p1.full_additional_name,
                                   ppc1.name,
                                   ppcs1.name,
                                   p1.full_form_name,
                                   p1.full_first_wrapping_name,
                                   p1.suffix,
                                   (
                                       CASE WHEN (p1.first_packing * p1.second_packing) > 0 AND p1.full_trade_name != '' AND p1.full_first_wrapping_name != '' AND (dsop1.name IS NULL OR (p1.first_packing * p1.second_packing) > 1)
                                                THEN '№' || (p1.first_packing * p1.second_packing)
                                            ELSE ''
                                           END
                                       )
                           ) as "productDescription"
                    FROM kais_products p1
                    LEFT JOIN kais_product_person_categories ppc1 ON ppc1.id = p1.person_category_id
                    LEFT JOIN kais_product_person_category_suffixes ppcs1 ON ppcs1.id = p1.person_category_suffix_id
                    LEFT JOIN dont_show_one_pack dsop1 ON p1.full_first_wrapping_name LIKE (dsop1.name || '%')
                    WHERE p1.kais_product_id = p.kais_product_id
                    LIMIT 1
                ) description ON TRUE
                WHERE rp.retail_product_id IN (:retailProductIds)
            SQL;

        return $this->connection->executeQuery(
            $sql,
            [
                'retailProductIds' => $retailProductIds,
            ],
            [
                'retailProductIds' => ArrayParameterType::INTEGER,
            ]
        )->fetchAllAssociative();
    }

    /**
     * @param list<int> $kaisProductIds
     *
     * @return list<array{string, int}>
     */
    public function getRetailProductIdsByKaisIds(array $kaisProductIds): array
    {
        $sql = <<<SQL
                SELECT kais_product_id, retail_product_id
                FROM retail_products
                WHERE kais_product_id IN (:kaisProductIds)
            SQL;

        return $this->connection->executeQuery(
            $sql,
            [
                'kaisProductIds' => $kaisProductIds,
            ],
            [
                'kaisProductIds' => ArrayParameterType::INTEGER,
            ]
        )->fetchAllKeyValue();
    }
}
