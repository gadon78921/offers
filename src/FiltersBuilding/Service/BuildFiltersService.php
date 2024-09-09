<?php

declare(strict_types=1);

namespace App\FiltersBuilding\Service;

use App\FiltersBuilding\DatabaseAccessObjects\AssortmentUnitSpecificationDatabaseAccessObject;
use App\FiltersBuilding\DatabaseAccessObjects\FilterDatabaseAccessObject;
use App\FiltersBuilding\Filters\FilterExtractors;

final class BuildFiltersService
{
    public function __construct(
        private readonly AssortmentUnitSpecificationDatabaseAccessObject $assortmentUnitSpecificationDao,
        private readonly FilterExtractors $filterExtractors,
        private readonly FilterDatabaseAccessObject $filterDao,
    ) {}

    /**
     * @param array<int> $assortmentUnitIds
     */
    public function build(array $assortmentUnitIds, int $limit = 1000, int $offset = 0): void
    {
        while (true) {
            $filters                      = [];
            $assortmentUnitSpecifications = $this->assortmentUnitSpecificationDao->fetch($assortmentUnitIds, $limit, $offset);

            foreach ($assortmentUnitSpecifications as $assortmentUnitSpecification) {
                $assortmentUnitId           = $assortmentUnitSpecification['assortmentUnitId'];
                $filters[$assortmentUnitId] = $this->filterExtractors->extractFilters($assortmentUnitSpecification);
            }

            if (!empty($filters)) {
                $this->filterDao->saveBulk($filters);
            }

            if (count($filters) < $limit) {
                break;
            }

            $offset += $limit;
        }
    }
}
