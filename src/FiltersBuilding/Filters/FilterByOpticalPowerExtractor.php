<?php

declare(strict_types=1);

namespace App\FiltersBuilding\Filters;

final class FilterByOpticalPowerExtractor implements FilterExtractorInterface
{
    /**
     * @param array{'assortmentUnitId': int, 'activeSubstance': string, 'brand': string, 'categoryName': string, 'dosage': string, 'dosageForm': string, 'manufacturerCountryName': string, 'manufacturerName': string, 'packageQuantity': int, 'sellProcedure': string, 'subtitle': string} $assortmentUnitSpecification
     *
     * @return array{opticalPower: string}
     */
    public function extract(array $assortmentUnitSpecification): array
    {
        $matchesCount = preg_match('/\(([-+])(\d+\.\d+)\)/', $assortmentUnitSpecification['subtitle'], $matches);

        if ($matchesCount > 0) {
            $opticalPower = ('0.00' === $matches[2] ? '' : $matches[1]) . $matches[2];
        }

        return ['opticalPower' => $opticalPower ?? 'не указана'];
    }
}
