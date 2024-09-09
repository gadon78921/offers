<?php

declare(strict_types=1);

namespace App\FiltersBuilding\Filters;

final class FilterExtractors
{
    /** @var array<int, FilterExtractorInterface> */
    private array $filterExtractors;

    public function __construct()
    {
        $this->filterExtractors = [
            new FilterByActiveSubstanceExtractor(),
            new FilterByApplicationAreaExtractor(),
            new FilterByAtomizerTypeExtractor(),
            new FilterByBrandExtractor(),
            new FilterByCategoryExtractor(),
            new FilterByDosageExtractor(),
            new FilterByDosageFormExtractor(),
            new FilterByLensCurvatureRadiusExtractor(),
            new FilterByManufacturerCountryNameExtractor(),
            new FilterByManufacturerNameExtractor(),
            new FilterByMinimumAgeExtractor(),
            new FilterByOpticalPowerExtractor(),
            new FilterByPackageQuantityExtractor(),
            new FilterByPackageVolumeExtractor(),
            new FilterBySellProcedureExtractor(),
        ];
    }

    /**
     * @param array{'assortmentUnitId': int, 'activeSubstance': string, 'brand': string, 'categoryName': string, 'dosage': string, 'dosageForm': string, 'manufacturerCountryName': string, 'manufacturerName': string, 'packageQuantity': int, 'sellProcedure': string, 'subtitle': string} $assortmentUnitSpecification
     *
     * @return array{string: string}
     */
    public function extractFilters(array $assortmentUnitSpecification): array
    {
        $filters = [[]];

        /** @var FilterExtractorInterface $filterExtractor */
        foreach ($this->filterExtractors as $filterExtractor) {
            $filters[] = $filterExtractor->extract($assortmentUnitSpecification);
        }

        return array_merge(...$filters);
    }
}
