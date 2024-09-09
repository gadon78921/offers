<?php

declare(strict_types=1);

namespace App\FiltersBuilding\Filters;

final class FilterByLensCurvatureRadiusExtractor implements FilterExtractorInterface
{
    /**
     * @param array{'assortmentUnitId': int, 'activeSubstance': string, 'brand': string, 'categoryName': string, 'dosage': string, 'dosageForm': string, 'manufacturerCountryName': string, 'manufacturerName': string, 'packageQuantity': int, 'sellProcedure': string, 'subtitle': string} $assortmentUnitSpecification
     *
     * @return array{lensCurvatureRadius: string}
     */
    public function extract(array $assortmentUnitSpecification): array
    {
        $matchesCount        = preg_match('/(R(?:8\.\d|9))\s/', $assortmentUnitSpecification['subtitle'], $matches);
        $lensCurvatureRadius = $matchesCount > 0 ? $matches[1] : 'не указан';

        return ['lensCurvatureRadius' => $lensCurvatureRadius];
    }
}
