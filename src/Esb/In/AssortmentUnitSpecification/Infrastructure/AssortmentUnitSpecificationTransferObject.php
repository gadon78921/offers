<?php

declare(strict_types=1);

namespace App\Esb\In\AssortmentUnitSpecification\Infrastructure;

final class AssortmentUnitSpecificationTransferObject
{
    public function __construct(
        public readonly int $assortmentUnitId,
        public readonly int $baseProductId,
        public readonly int $packageQuantity,
        public readonly int $prescriptionForm,
        public readonly int $quantityInPrimaryPack,
        public readonly int $quantityInSecondaryPack,
        public readonly string $isVital,
        public readonly string $isStorageTypeCold,
        public readonly string $canUnpackPrimary,
        public readonly string $canUnpackSecondary,
        public readonly string $fullProductName,
        public readonly string $productName,
        public readonly string $subtitle,
        public readonly string $manufacturerName,
        public readonly string $manufacturerCountryName,
        public readonly ?string $activeSubstance,
        public readonly ?string $dosage,
        public readonly ?string $dosageForm,
        public readonly ?string $sellProcedure,
        public readonly ?string $brand,
        public readonly ?string $description,
        public readonly ?string $composition,
        public readonly ?string $indicationsForUse,
        public readonly ?string $contraindicationsForUse,
        public readonly ?string $pharmokinetic,
        public readonly ?string $pharmodynamic,
        public readonly ?string $methodOfUse,
        public readonly ?string $sideEffect,
        public readonly ?string $storageConditions,
        public readonly ?string $wrappingName,
        public readonly ?int $expirationDate,
        public readonly ?int $numberOfDoses,
    ) {}
}
