<?php

declare(strict_types=1);

namespace App\FiltersBuilding\Filters;

final class FilterByAtomizerTypeExtractor implements FilterExtractorInterface
{
    /**
     * @param array{'assortmentUnitId': int, 'activeSubstance': string, 'brand': string, 'categoryName': string, 'dosage': string, 'dosageForm': string, 'manufacturerCountryName': string, 'manufacturerName': string, 'packageQuantity': int, 'sellProcedure': string, 'subtitle': string} $assortmentUnitSpecification
     *
     * @return array{atomizerType: string}
     */
    public function extract(array $assortmentUnitSpecification): array
    {
        $matchesCount = preg_match('/(?:^|\s)(струя|душ)(?:$|\s)|(?:^|\s|-|\()(спрей)(?!,)|(?:^|(?<!\d)\s)(капли)(?:$|\s)/', $assortmentUnitSpecification['subtitle'], $matches);

        if ($matchesCount > 0) {
            if (!empty($matches[1])) {
                $atomizerType = $matches[1];
            } elseif (!empty($matches[2])) {
                $atomizerType = $matches[2];
            } else {
                $atomizerType = $matches[3];
            }
        }

        return ['atomizerType' => $atomizerType ?? 'не указан'];
    }
}
