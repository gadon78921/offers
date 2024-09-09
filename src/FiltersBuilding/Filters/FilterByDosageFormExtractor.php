<?php

declare(strict_types=1);

namespace App\FiltersBuilding\Filters;

final class FilterByDosageFormExtractor implements FilterExtractorInterface
{
    /**
     * @param array{'assortmentUnitId': int, 'activeSubstance': string, 'brand': string, 'categoryName': string, 'dosage': string, 'dosageForm': string, 'manufacturerCountryName': string, 'manufacturerName': string, 'packageQuantity': int, 'sellProcedure': string, 'subtitle': string} $assortmentUnitSpecification
     *
     * @return array{dosageForm: string}
     */
    public function extract(array $assortmentUnitSpecification): array
    {
        $dosageForm = $assortmentUnitSpecification['dosageForm'] ?? 'не указана';

        if ('таблетки' === $dosageForm) {
            if (str_contains($assortmentUnitSpecification['subtitle'], 'таблетки диспергируемые')) {
                $dosageForm = 'таблетки растворимые';
            } elseif (str_contains($assortmentUnitSpecification['subtitle'], 'таблетки растворимые')) {
                $dosageForm = 'таблетки растворимые';
            } elseif (str_contains($assortmentUnitSpecification['subtitle'], 'таблетки для приготовления раствора')) {
                $dosageForm = 'таблетки растворимые';
            } elseif (str_contains($assortmentUnitSpecification['subtitle'], 'таблетки шипучие')) {
                $dosageForm = 'таблетки шипучие';
            } elseif (str_contains($assortmentUnitSpecification['subtitle'], 'таблетки жевательные')) {
                $dosageForm = 'таблетки жевательные';
            } elseif (str_contains($assortmentUnitSpecification['subtitle'], 'таблетки для рассасывания')) {
                $dosageForm = 'таблетки для рассасывания';
            }
        }

        return ['dosageForm' => $dosageForm];
    }
}
