<?php

declare(strict_types=1);

namespace App\FiltersBuilding\Filters;

final class FilterByDosageExtractor implements FilterExtractorInterface
{
    /**
     * @param array{'assortmentUnitId': int, 'activeSubstance': string, 'brand': string, 'categoryName': string, 'dosage': string, 'dosageForm': string, 'manufacturerCountryName': string, 'manufacturerName': string, 'packageQuantity': int, 'sellProcedure': string, 'subtitle': string} $assortmentUnitSpecification
     *
     * @return array{dosage: string}
     */
    public function extract(array $assortmentUnitSpecification): array
    {
        return ['dosage' => $assortmentUnitSpecification['dosage'] ?? 'не указана'];
    }
}
