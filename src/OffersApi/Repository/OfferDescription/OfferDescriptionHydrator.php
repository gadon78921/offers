<?php

declare(strict_types=1);

namespace App\OffersApi\Repository\OfferDescription;

use App\OffersApi\Domain\Entity\Offer\OfferDescription;

final class OfferDescriptionHydrator
{
    /** @param array{'assortmentUnitId': int, 'baseProductIdInUrl': int, 'kaisProductId': int, 'kaisProductCode': int, 'retailProductCode': int, 'transliteratedFullTradeName': string, 'manufacturerName': string, 'manufacturerCountryName': string, 'productName': string, 'fullProductName': string, 'subtitle': string, 'sellProcedure': string|null, 'isVital': bool, 'activeSubstance': string|null, 'canUnpackPrimary': bool, 'canUnpackSecondary': bool, 'packageQuantity': int, 'quantityInPrimaryPack': int, 'quantityInSecondaryPack': int, 'brand': string|null, 'dosage': string|null, 'dosageForm': string|null, 'isStorageTypeCold': bool, 'expirationDate': int|null, 'prescriptionForm': int, 'wrappingName': string|null, 'numberOfDoses': int|null, 'groupPath': string|null} $offerDescriptionData */
    public function hydrateOfferDescription(array $offerDescriptionData): OfferDescription
    {
        return new OfferDescription(
            $offerDescriptionData['baseProductIdInUrl'],
            $offerDescriptionData['kaisProductId'],
            $offerDescriptionData['kaisProductCode'],
            $offerDescriptionData['retailProductCode'],
            $offerDescriptionData['transliteratedFullTradeName'],
            $offerDescriptionData['manufacturerName'],
            $offerDescriptionData['manufacturerCountryName'],
            $offerDescriptionData['productName'],
            $offerDescriptionData['fullProductName'],
            $offerDescriptionData['subtitle'],
            $offerDescriptionData['sellProcedure'],
            $offerDescriptionData['isVital'],
            $offerDescriptionData['activeSubstance'],
            $offerDescriptionData['canUnpackPrimary'],
            $offerDescriptionData['canUnpackSecondary'],
            $offerDescriptionData['packageQuantity'],
            $offerDescriptionData['quantityInPrimaryPack'],
            $offerDescriptionData['quantityInSecondaryPack'],
            $offerDescriptionData['brand'],
            $offerDescriptionData['dosage'],
            $offerDescriptionData['dosageForm'],
            $offerDescriptionData['isStorageTypeCold'],
            $offerDescriptionData['expirationDate'],
            $offerDescriptionData['prescriptionForm'],
            $offerDescriptionData['wrappingName'],
            $offerDescriptionData['numberOfDoses'],
            $offerDescriptionData['groupPath'],
        );
    }
}
