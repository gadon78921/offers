<?php

declare(strict_types=1);

namespace App\OffersApi\Repository\OfferDescription;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;

final class OfferDescriptionDatabaseAccessObject
{
    public function __construct(
        private readonly Connection $connection,
    ) {}

    /**
     * @param array<int> $assortmentUnitIds
     *
     * @return \Traversable<int, array{'assortmentUnitId': int, 'baseProductIdInUrl': int, 'kaisProductId': int, 'kaisProductCode': int, 'retailProductCode': int, 'transliteratedFullTradeName': string, 'manufacturerName': string|null, 'manufacturerCountryName': string, 'productName': string, 'fullProductName': string, 'subtitle': string, 'sellProcedure': string|null, 'isVital': bool, 'activeSubstance': string|null, 'canUnpackPrimary': bool, 'canUnpackSecondary': bool, 'packageQuantity': int, 'quantityInPrimaryPack': int, 'quantityInSecondaryPack': int, 'brand': string|null, 'dosage': string|null, 'dosageForm': string|null, 'isStorageTypeCold': bool, 'expirationDate': int|null, 'prescriptionForm': int, 'wrappingName': string|null, 'numberOfDoses': int|null, 'groupPath': string|null}>
     */
    public function fetch(array $assortmentUnitIds): \Traversable
    {
        $sql = <<<SQL
                SELECT
                    aus.assortment_unit_id            as "assortmentUnitId",
                    aus.base_product_id               as "baseProductIdInUrl",
                    rp.kais_product_id                as "kaisProductId",
                    kp.kais_product_code              as "kaisProductCode",
                    rp.retail_product_code            as "retailProductCode",
                    kp.transliterated_full_trade_name as "transliteratedFullTradeName",
                    aus.manufacturer_name             as "manufacturerName",
                    aus.manufacturer_country_name     as "manufacturerCountryName",
                    aus.product_name                  as "productName",
                    aus.full_product_name             as "fullProductName",
                    aus.subtitle                      as "subtitle",
                    aus.sell_procedure                as "sellProcedure",
                    aus.is_vital                      as "isVital",
                    aus.active_substance              as "activeSubstance",
                    aus.can_unpack_primary            as "canUnpackPrimary",
                    aus.can_unpack_secondary          as "canUnpackSecondary",
                    aus.package_quantity              as "packageQuantity",
                    aus.quantity_in_primary_pack      as "quantityInPrimaryPack",
                    aus.quantity_in_secondary_pack    as "quantityInSecondaryPack",
                    aus.brand                         as "brand",
                    aus.dosage                        as "dosage",
                    aus.dosage_form                   as "dosageForm",
                    aus.is_storage_type_cold          as "isStorageTypeCold",
                    aus.expiration_date               as "expirationDate",
                    aus.prescription_form             as "prescriptionForm",
                    aus.wrapping_name                 as "wrappingName",
                    aus.number_of_doses               as "numberOfDoses",
                    pgp.path                          as "groupPath"
                FROM assortment_unit_specifications aus
                JOIN retail_products rp ON rp.retail_product_id = aus.base_product_id
                JOIN kais_products kp ON kp.kais_product_id = rp.kais_product_id
                LEFT JOIN products_groups_path pgp ON pgp.assortment_unit_id = aus.assortment_unit_id
                WHERE aus.assortment_unit_id IN (:assortmentUnitIds)
            SQL;

        return $this->connection->executeQuery($sql, ['assortmentUnitIds' => $assortmentUnitIds], ['assortmentUnitIds' => ArrayParameterType::INTEGER])->iterateAssociative();
    }
}
