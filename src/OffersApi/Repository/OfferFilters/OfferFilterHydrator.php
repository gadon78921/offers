<?php

declare(strict_types=1);

namespace App\OffersApi\Repository\OfferFilters;

use App\OffersApi\Domain\Entity\Offer\OfferFilter;
use App\OffersApi\Domain\Entity\Offer\OfferFilterPossibleValue;
use App\OffersApi\Domain\Entity\Offer\OfferFilterView;
use Doctrine\Common\Collections\ArrayCollection;

final class OfferFilterHydrator
{
    /**
     * @param array{
     *     'type'                    : string,
     *     'itemType'                : string,
     *     'title'                   : string,
     *     'viewType'                : string,
     *     'gender'                  : string,
     *     'isAvailableForFastAccess': bool,
     *     'name'                    : string,
     *     'possibleValues'          : string
     * } $filter
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
    public function hydrateOfferFilter(array $filter, array $filtersValues): OfferFilter
    {
        $possibleValuesCollection = new ArrayCollection();
        $possibleValues           = json_decode($filter['possibleValues'], true, 512, JSON_THROW_ON_ERROR);
        foreach ($possibleValues as $possibleValue) {
            $possibleValuesCollection->add(new OfferFilterPossibleValue(
                (string) $possibleValue['key'],
                (string) $possibleValue['value'],
                (int) $possibleValue['count'],
            ));
        }

        $offerFilterView = new OfferFilterView(
            (string) $filter['title'],
            (string) $filter['viewType'],
            (string) $filter['gender'],
        );

        return new OfferFilter(
            (string) $filter['type'],
            (string) $filter['itemType'],
            (string) $filter['name'],
            $offerFilterView,
            $possibleValuesCollection,
            (bool) $filter['isAvailableForFastAccess'],
            $filtersValues[$filter['name']] ?? [],
        );
    }
}
