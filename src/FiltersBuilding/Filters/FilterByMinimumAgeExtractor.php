<?php

declare(strict_types=1);

namespace App\FiltersBuilding\Filters;

final class FilterByMinimumAgeExtractor implements FilterExtractorInterface
{
    /**
     * @param array{'assortmentUnitId': int, 'activeSubstance': string, 'brand': string, 'categoryName': string, 'dosage': string, 'dosageForm': string, 'manufacturerCountryName': string, 'manufacturerName': string, 'packageQuantity': int, 'sellProcedure': string, 'subtitle': string} $assortmentUnitSpecification
     *
     * @return array{minimumAge: string}
     */
    public function extract(array $assortmentUnitSpecification): array
    {
        $matchesCount = preg_match('/(?:(?:(?:с|от)\s(\d+(?:,\d+)?)\s)(?:до\s\d+\s?)?(мес|лет|года?)|(?:(?:до)\s(\d+)\s?)(?:мес|лет|года?)|(?:(\d+)\-\d+лет))/', $assortmentUnitSpecification['subtitle'], $matches);

        if ($matchesCount > 0) {
            if ('' !== $matches[1] && '' !== $matches[2]) {
                $count = str_replace(',', '.', $matches[1]);
                $units = $matches[2];
            } elseif ('' !== $matches[3]) { // до 4 лет
                $count = '0';
                $units = 'мес';
            } else { // 8-15лет
                $count = $matches[4];
                $units = 'лет';
            }
            $minimumAge = ((float) $count) * ('мес' === $units ? 1 : 12);

            if ($minimumAge > 12 && 'мес' === $units) { // с 18 мес -> с 1,5 лет
                $count = (string) ($minimumAge / 12);
                $units = 'лет';
            } elseif ('0' === $count) { // с 0 до 1 года -> с 0 мес
                $units = 'мес';
            } elseif ('1' === $count && in_array($units, ['лет', 'года'])) { // с 1 года -> с 12 мес, с 1 до 10 лет -> с 12 мес
                $count = '12';
                $units = 'мес';
            }
            $minimumAgeTitle = "с $count $units";
        }

        return ['minimumAge' => $minimumAgeTitle ?? 'не указан'];
    }
}
