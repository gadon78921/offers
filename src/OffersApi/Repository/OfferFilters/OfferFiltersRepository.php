<?php

declare(strict_types=1);

namespace App\OffersApi\Repository\OfferFilters;

use App\OffersApi\Domain\Entity\Offer\OfferFilterCollection;

final class OfferFiltersRepository
{
    public function __construct(
        private readonly OfferFiltersDatabaseAccessObject $dao,
        private readonly OfferFilterHydrator $hydrator,
    ) {}

    /**
     * @param array<int> $assortmentUnitIds
     * @param array{
     *     'activeSubstance'?    : list<string>,
     *     'applicationArea'?    : list<string>,
     *     'atomizerType'?       : list<string>,
     *     'brand'?              : list<string>,
     *     'category'?           : list<string>,
     *     'country'?            : list<string>,
     *     'dosage'?             : list<string>,
     *     'dosageForm'?         : list<string>,
     *     'inTradePoints'?      : list<string>,
     *     'lensCurvatureRadius'?: list<string>,
     *     'manufacturer'?       : list<string>,
     *     'minimumAge'?         : list<string>,
     *     'opticalPower'?       : list<string>,
     *     'packageQuantity'?    : list<string>,
     *     'packageVolume'?      : list<string>,
     *     'sellProcedure'?      : list<string>
     * } $filtersValues
     */
    public function getFiltersByAssortmentUnitIds(string $kladrId, array $assortmentUnitIds, array $filtersValues): OfferFilterCollection
    {
        $collection = new OfferFilterCollection();
        foreach ($this->dao->fetchPossibleFiltersByAssortmentUnitIdsAndKladrId($kladrId, $assortmentUnitIds) as $filter) {
            $collection->values->add($this->hydrator->hydrateOfferFilter($filter, $filtersValues));
        }

        return $collection;
    }

    /**
     * @param array{
     *     'activeSubstance'?    : list<string>,
     *     'applicationArea'?    : list<string>,
     *     'atomizerType'?       : list<string>,
     *     'brand'?              : list<string>,
     *     'category'?           : list<string>,
     *     'country'?            : list<string>,
     *     'dosage'?             : list<string>,
     *     'dosageForm'?         : list<string>,
     *     'inTradePoints'?      : list<string>,
     *     'lensCurvatureRadius'?: list<string>,
     *     'manufacturer'?       : list<string>,
     *     'minimumAge'?         : list<string>,
     *     'opticalPower'?       : list<string>,
     *     'packageQuantity'?    : list<string>,
     *     'packageVolume'?      : list<string>,
     *     'sellProcedure'?      : list<string>
     * } $filtersValues
     */
    public function getFiltersByCategoryId(string $kladrId, int $categoryId, array $filtersValues): OfferFilterCollection
    {
        $collection = new OfferFilterCollection();
        foreach ($this->dao->fetchPossibleFiltersByCategoryIdAndKladrId($kladrId, $categoryId) as $filter) {
            $collection->values->add($this->hydrator->hydrateOfferFilter($filter, $filtersValues));
        }

        return $collection;
    }

    /**
     * @param array<int>                            $assortmentUnitIds
     * @param array<string, array<int, int|string>> $filters
     *
     * @return array<int>
     */
    public function filterAssortmentUnitIds(array $assortmentUnitIds, string $kladrId, array $filters): array
    {
        return $this->dao->filterAssortmentUnitIds($assortmentUnitIds, $kladrId, $filters);
    }

    /**
     * @param array<string, array<int, int|string>> $filters
     *
     * @return array<int>
     */
    public function filterByCategoryId(int $categoryId, string $kladrId, array $filters): array
    {
        return $this->dao->filterByCategoryId($categoryId, $kladrId, $filters);
    }
}
