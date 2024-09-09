<?php

declare(strict_types=1);

namespace App\FiltersBuilding\Filters;

final class FilterByBrandExtractor implements FilterExtractorInterface
{
    /**
     * @param array{'assortmentUnitId': int, 'activeSubstance': string, 'brand': string, 'categoryName': string, 'dosage': string, 'dosageForm': string, 'manufacturerCountryName': string, 'manufacturerName': string, 'packageQuantity': int, 'sellProcedure': string, 'subtitle': string} $assortmentUnitSpecification
     *
     * @return array{brand: string}
     */
    public function extract(array $assortmentUnitSpecification): array
    {
        if (null !== $assortmentUnitSpecification['brand']) {
            $matchesCount = preg_match('/.+\(([^\x{0400}-\x{04FF}]+)\)/u', $assortmentUnitSpecification['brand'], $matches);
            $brand        = $matchesCount > 0 ? $matches[1] : $assortmentUnitSpecification['brand'];
        }

        return ['brand' => $brand ?? 'не указан'];
    }
}
