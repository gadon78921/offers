<?php

declare(strict_types=1);

namespace App\FiltersBuilding\Filters;

final class FilterByApplicationAreaExtractor implements FilterExtractorInterface
{
    /**
     * @param array{'assortmentUnitId': int, 'activeSubstance': string, 'brand': string, 'categoryName': string, 'dosage': string, 'dosageForm': string, 'manufacturerCountryName': string, 'manufacturerName': string, 'packageQuantity': int, 'sellProcedure': string, 'subtitle': string} $assortmentUnitSpecification
     *
     * @return array{applicationArea: string}
     */
    public function extract(array $assortmentUnitSpecification): array
    {
        $matchesCount = preg_match('/\s(горл[ае])|(?:^|\s)(нос)(?:\s|а|о[^к])|(?:^|\s)(назальн)|(?:^|\s)(насморк)|(?:^|\s)(ушн)|(?:^(?:Г|г)|\sг|\/г)(лаз)[ан\s\/]/u', $assortmentUnitSpecification['subtitle'], $matches);

        if ($matchesCount > 0) {
            if (!empty($matches[1])) {
                $applicationArea = 'горло';
            } elseif (!empty($matches[2]) || !empty($matches[3]) || !empty($matches[4])) {
                $applicationArea = 'нос';
            } elseif (!empty($matches[5])) {
                $applicationArea = 'ухо';
            } else {
                $applicationArea = 'глаза';
            }
        }

        return ['applicationArea' => $applicationArea ?? 'не указана'];
    }
}
