<?php

declare(strict_types=1);

namespace App\OffersApi\Domain\Entity\Offer;

final class OfferDescription
{
    public function __construct(
        public readonly int $baseProductIdInUrl,
        public readonly int $kaisProductId,
        public readonly int $kaisProductCode,
        public readonly int $retailProductCode,
        public readonly ?string $transliteratedFullTradeName,
        public readonly string $manufacturerName,
        public readonly string $manufacturerCountryName,
        public readonly string $productName,
        public readonly string $fullProductName,
        public readonly string $subtitle,
        public readonly ?string $sellProcedure,
        public readonly bool $isVital,
        public readonly ?string $activeSubstance,
        public readonly bool $canUnpackPrimary,
        public readonly bool $canUnpackSecondary,
        public readonly int $packageQuantity,
        public readonly int $quantityInPrimaryPack,
        public readonly int $quantityInSecondaryPack,
        public readonly ?string $brand,
        public readonly ?string $dosage,
        public readonly ?string $dosageForm,
        public readonly bool $isStorageTypeCold,
        public readonly ?int $expirationDate,
        public readonly int $prescriptionForm,
        public readonly ?string $wrappingName,
        public readonly ?int $numberOfDoses,
        public readonly ?string $groupPath,
    ) {}
}
