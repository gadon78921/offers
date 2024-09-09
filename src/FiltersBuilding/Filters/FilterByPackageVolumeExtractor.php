<?php

declare(strict_types=1);

namespace App\FiltersBuilding\Filters;

final class FilterByPackageVolumeExtractor implements FilterExtractorInterface
{
    /**
     * @param array{'assortmentUnitId': int, 'activeSubstance': string, 'brand': string, 'categoryName': string, 'dosage': string, 'dosageForm': string, 'manufacturerCountryName': string, 'manufacturerName': string, 'packageQuantity': int, 'sellProcedure': string, 'subtitle': string} $assortmentUnitSpecification
     *
     * @return array{packageVolume: string}
     */
    public function extract(array $assortmentUnitSpecification): array
    {
        $matchesCount = preg_match_all('/((?:\d+[\.,])?\d+)\s?(л|мл)(?:ит|\s|\+|\)|$)/', $assortmentUnitSpecification['subtitle'], $matches);

        if (1 === $matchesCount) {
            $count              = str_replace(',', '.', $matches[1][0]);
            $units              = $matches[2][0];
            $packageVolumeTitle = "$count $units";
        }

        return ['packageVolume' => $packageVolumeTitle ?? 'не указан'];
    }
}
