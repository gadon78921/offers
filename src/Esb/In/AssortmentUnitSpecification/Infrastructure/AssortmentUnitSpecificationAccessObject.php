<?php

declare(strict_types=1);

namespace App\Esb\In\AssortmentUnitSpecification\Infrastructure;

use Doctrine\DBAL\Connection;

final class AssortmentUnitSpecificationAccessObject
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    public function save(AssortmentUnitSpecificationTransferObject $assortmentUnitSpecification): void
    {
        $sql = '
            INSERT INTO assortment_unit_specifications (
                assortment_unit_id,
                base_product_id,
                package_quantity,
                prescription_form,
                quantity_in_primary_pack,
                quantity_in_secondary_pack,
                is_vital,
                is_storage_type_cold,
                can_unpack_primary,
                can_unpack_secondary,
                full_product_name,
                product_name,
                subtitle,
                manufacturer_name,
                manufacturer_country_name,
                active_substance,
                dosage,
                dosage_form,
                sell_procedure,
                brand,
                description,
                composition,
                indications_for_use,
                contraindications_for_use,
                pharmokinetic,
                pharmodynamic,
                method_of_use,
                side_effect,
                storage_conditions,
                wrapping_name,
                expiration_date,
                number_of_doses
            )
            VALUES (
                :assortmentUnitId,
                :baseProductId,
                :packageQuantity,
                :prescriptionForm,
                :quantityInPrimaryPack,
                :quantityInSecondaryPack,
                :isVital,
                :isStorageTypeCold,
                :canUnpackPrimary,
                :canUnpackSecondary,
                :fullProductName,
                :productName,
                :subtitle,
                :manufacturerName,
                :manufacturerCountryName,
                :activeSubstance,
                :dosage,
                :dosageForm,
                :sellProcedure,
                :brand,
                :description,
                :composition,
                :indicationsForUse,
                :contraindicationsForUse,
                :pharmokinetic,
                :pharmodynamic,
                :methodOfUse,
                :sideEffect,
                :storageConditions,
                :wrappingName,
                :expirationDate,
                :numberOfDoses
            )
            ON CONFLICT (assortment_unit_id) DO UPDATE SET
                base_product_id = EXCLUDED.base_product_id,
                package_quantity = EXCLUDED.package_quantity,
                prescription_form = EXCLUDED.prescription_form,
                quantity_in_primary_pack = EXCLUDED.quantity_in_primary_pack,
                quantity_in_secondary_pack = EXCLUDED.quantity_in_secondary_pack,
                is_vital = EXCLUDED.is_vital,
                is_storage_type_cold = EXCLUDED.is_storage_type_cold,
                can_unpack_primary = EXCLUDED.can_unpack_primary,
                can_unpack_secondary = EXCLUDED.can_unpack_secondary,
                full_product_name = EXCLUDED.full_product_name,
                product_name = EXCLUDED.product_name,
                subtitle = EXCLUDED.subtitle,
                manufacturer_name = EXCLUDED.manufacturer_name,
                manufacturer_country_name = EXCLUDED.manufacturer_country_name,
                active_substance = EXCLUDED.active_substance,
                dosage = EXCLUDED.dosage,
                dosage_form = EXCLUDED.dosage_form,
                sell_procedure = EXCLUDED.sell_procedure,
                brand = EXCLUDED.brand,
                description = EXCLUDED.description,
                composition = EXCLUDED.composition,
                indications_for_use = EXCLUDED.indications_for_use,
                contraindications_for_use = EXCLUDED.contraindications_for_use,
                pharmokinetic = EXCLUDED.pharmokinetic,
                pharmodynamic = EXCLUDED.pharmodynamic,
                method_of_use = EXCLUDED.method_of_use,
                side_effect = EXCLUDED.side_effect,
                storage_conditions = EXCLUDED.storage_conditions,
                wrapping_name = EXCLUDED.wrapping_name,
                expiration_date = EXCLUDED.expiration_date,
                number_of_doses = EXCLUDED.number_of_doses
        ';

        $this->connection->executeStatement($sql, get_object_vars($assortmentUnitSpecification));
    }

    public function remove(int $assortmentUnitId): void
    {
        $sql = 'DELETE FROM assortment_unit_specifications WHERE assortment_unit_id = :assortmentUnitId';
        $this->connection->executeStatement($sql, ['assortmentUnitId' => $assortmentUnitId]);
    }
}
