<?php

namespace App\FiltersBuilding\Filters;

interface FilterExtractorInterface
{
    /**
     * @param array{'assortmentUnitId': int, 'activeSubstance': string, 'brand': string, 'categoryName': string, 'dosage': string, 'dosageForm': string, 'manufacturerCountryName': string, 'manufacturerName': string, 'packageQuantity': int, 'sellProcedure': string, 'subtitle': string} $assortmentUnitSpecification
     *
     * @return array<string>
     */
    public function extract(array $assortmentUnitSpecification): array;
}
