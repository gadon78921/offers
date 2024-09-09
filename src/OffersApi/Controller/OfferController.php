<?php

declare(strict_types=1);

namespace App\OffersApi\Controller;

use App\OffersApi\Domain\Entity\Offer\Offer;
use App\OffersApi\Domain\Entity\Offer\OfferFilter;
use App\OffersApi\Repository\KaisProducts\KaisProductsDatabaseAccessObject;
use App\OffersApi\Repository\Offer\OfferDatabaseAccessObject;
use App\OffersApi\Repository\Offer\OfferRepository;
use App\OffersApi\Repository\OfferFilters\OfferFiltersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class OfferController extends AbstractController
{
    public function __construct(
        private readonly OfferRepository $repository,
        private readonly OfferFiltersRepository $offerFiltersRepository,
        private readonly KaisProductsDatabaseAccessObject $kaisProductsDao,
        private readonly OfferDatabaseAccessObject $dao,
    ) {}

    /**
     * @param list<int> $assortmentUnitIds
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
     * } $filters
     *
     * @return array{
     *     'totalCount'      : int,
     *     'offers'          : list<Offer>,
     *     'possibleFilters' : list<OfferFilter>,
     *     'fastAccessFilter': array{
     *         'prevFilters': list<OfferFilter>,
     *         'nextFilter' : OfferFilter|null
     *     }
     * }
     */
    #[Route('/offers/get-by-assortment-unit-ids-and-kladr-id', name: 'offers-get-by-assortment-unit-ids-and-kladr-id')]
    #[ParamConverter('assortmentUnitIds', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('kladrId', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('limit', options: ['default' => 1000], isOptional: true, converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('offset', options: ['default' => 0], isOptional: true, converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('sortBy', options: ['default' => null], isOptional: true, converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('sortOrder', options: ['default' => 'ASC'], isOptional: true, converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('filters', options: ['default' => []], isOptional: true, converter: 'json.request_body_param_fetcher')]
    public function getByAssortmentUnitIdsAndKladrId(array $assortmentUnitIds, string $kladrId, int $limit = 10, int $offset = 0, ?string $sortBy = null, string $sortOrder = 'asc', array $filters = []): array
    {
        $filteredAssortmentUnitIds = $assortmentUnitIds;

        if (!empty($filters)) {
            $filteredAssortmentUnitIds = $this->offerFiltersRepository->filterAssortmentUnitIds($assortmentUnitIds, $kladrId, $filters);
        }

        $offers                = $this->repository->getOffers($kladrId, $filteredAssortmentUnitIds, $limit, $offset, $sortBy, $sortOrder);
        $offerFilterCollection = $this->offerFiltersRepository->getFiltersByAssortmentUnitIds($kladrId, $assortmentUnitIds, $filters);

        $filteredOfferFilterCollection = $offerFilterCollection;

        if (count($assortmentUnitIds) !== count($filteredAssortmentUnitIds)) {
            $filteredOfferFilterCollection = $this->offerFiltersRepository->getFiltersByAssortmentUnitIds($kladrId, $filteredAssortmentUnitIds, $filters);
        }

        return [
            'totalCount'       => $offers['totalCount'] ?? 0,
            'offers'           => $offers['offers']->getValues() ?? [],
            'possibleFilters'  => $offerFilterCollection->getMoreThanOnePossibleValues()->getValues(),
            'fastAccessFilter' => [
                'prevFilters' => $offerFilterCollection->previousFastAccessFilter()->getValues(),
                'nextFilter'  => $filteredOfferFilterCollection->getNextFastAccessFilter(),
            ],
        ];
    }

    /**
     * @param list<int> $assortmentUnitIds
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
     * } $filters
     */
    #[Route('/offers/get-total-count-by-assortment-unit-ids-and-kladr-id', name: 'offers-get-total-count-by-assortment-unit-ids-and-kladr-id')]
    #[ParamConverter('assortmentUnitIds', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('kladrId', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('filters', options: ['default' => []], isOptional: true, converter: 'json.request_body_param_fetcher')]
    public function getTotalCountByAssortmentUnitIdsAndKladrId(array $assortmentUnitIds, string $kladrId, array $filters = []): int
    {
        $filteredAssortmentUnitIds = $assortmentUnitIds;

        if (!empty($filters)) {
            $filteredAssortmentUnitIds = $this->offerFiltersRepository->filterAssortmentUnitIds($assortmentUnitIds, $kladrId, $filters);
        }

        return $this->dao->fetchTotalCount($kladrId, $filteredAssortmentUnitIds);
    }

    /**
     * @param array<int> $retailProductIds
     *
     * @return array{'totalCount': int, 'offers': ArrayCollection<int, Offer>}
     */
    #[Route('/offers/get-by-retail-product-ids-and-kladr-id', name: 'offers-get-by-retail-product-ids-and-kladr-id')]
    #[ParamConverter('retailProductIds', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('kladrId', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    public function getByRetailProductIdsAndKladrId(array $retailProductIds, string $kladrId): array
    {
        return $this->repository->getOffersByRetailProductIdsAndKladrId($kladrId, $retailProductIds);
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
     * } $filters
     *
     * @return array{
     *     'totalCount'      : int,
     *     'offers'          : list<Offer>,
     *     'possibleFilters' : list<OfferFilter>,
     *     'fastAccessFilter': array{
     *         'prevFilters': list<OfferFilter>,
     *         'nextFilter' : OfferFilter|null
     *     }
     * }
     */
    #[Route('/offers/get-by-category-id-and-kladr-id', name: 'offers-get-by-category-id-and-kladr-id')]
    #[ParamConverter('categoryId', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('kladrId', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('limit', options: ['default' => 1000], isOptional: true, converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('offset', options: ['default' => 0], isOptional: true, converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('sortBy', options: ['default' => null], isOptional: true, converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('sortOrder', options: ['default' => 'ASC'], isOptional: true, converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('filters', options: ['default' => []], isOptional: true, converter: 'json.request_body_param_fetcher')]
    public function getByCategoryIdAndKladrId(int $categoryId, string $kladrId, int $limit = 10, int $offset = 0, ?string $sortBy = null, string $sortOrder = 'asc', array $filters = []): array
    {
        $assortmentUnitIds = $this->offerFiltersRepository->filterByCategoryId($categoryId, $kladrId, $filters);

        if (empty($assortmentUnitIds)) {
            return [
                'totalCount'       => 0,
                'offers'           => [],
                'possibleFilters'  => [],
                'fastAccessFilter' => [
                    'prevFilters' => [],
                    'nextFilter'  => null,
                ],
            ];
        }

        $offers        = $this->repository->getOffers($kladrId, $assortmentUnitIds, $limit, $offset, $sortBy, $sortOrder);
        $isEndCategory = $this->repository->isEndCategory($categoryId, $kladrId);

        if ($isEndCategory) {
            $offerFilterCollection    = $this->offerFiltersRepository->getFiltersByCategoryId($kladrId, $categoryId, $filters);
            $possibleFilters          = $offerFilterCollection->getMoreThanOnePossibleValues()->getValues();
            $previousFastAccessFilter = $offerFilterCollection->previousFastAccessFilter()->getValues();
            $nextFilter               = $offerFilterCollection->getNextFastAccessFilter();
        }

        if ($isEndCategory && !empty($filters)) {
            $filteredOfferFilterCollection = $this->offerFiltersRepository->getFiltersByAssortmentUnitIds($kladrId, $assortmentUnitIds, $filters);
            $nextFilter                    = $filteredOfferFilterCollection->getNextFastAccessFilter();
        }

        return [
            'totalCount'       => $offers['totalCount'],
            'offers'           => $offers['offers']->getValues(),
            'possibleFilters'  => $possibleFilters ?? [],
            'fastAccessFilter' => [
                'prevFilters' => $previousFastAccessFilter ?? [],
                'nextFilter'  => $nextFilter ?? null,
            ],
        ];
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
     * } $filters
     */
    #[Route('/offers/get-total-count-by-category-id-and-kladr-id', name: 'offers-get-total-count-by-category-id-and-kladr-id')]
    #[ParamConverter('categoryId', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('kladrId', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('filters', options: ['default' => []], isOptional: true, converter: 'json.request_body_param_fetcher')]
    public function getTotalCountByCategoryIdAndKladrId(int $categoryId, string $kladrId, array $filters = []): int
    {
        $assortmentUnitIds = $this->offerFiltersRepository->filterByCategoryId($categoryId, $kladrId, $filters);

        if (empty($assortmentUnitIds)) {
            return 0;
        }

        return $this->dao->fetchTotalCount($kladrId, $assortmentUnitIds);
    }

    /**
     * @return array{'totalCount': int, 'offers': ArrayCollection<int, Offer>}
     */
    #[Route('/offers/get-by-base-product-id-and-kladr-id', name: 'offers-get-by-base-product-id-and-kladr-id')]
    #[ParamConverter('baseProductId', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('kladrId', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    public function getByBaseProductIdAndKladrId(int $baseProductId, string $kladrId): array
    {
        return $this->repository->getOffersByBaseProductIdAndKladrId($baseProductId, $kladrId);
    }

    /**
     * @param array<int> $categoryIds
     *
     * @return array{'totalCount': int, 'offers': ArrayCollection<int, Offer>, 'totalCountOffersByCategoryIds': array<int, int>}
     */
    #[Route('/offers/get-grouped-by-category-ids', name: 'offers-get-grouped-by-category-ids')]
    #[ParamConverter('categoryIds', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('kladrId', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('limitInEachCategory', options: ['default' => 5], isOptional: true, converter: 'json.request_body_param_fetcher')]
    public function getGroupedByCategoryIdsAndKladrId(array $categoryIds, string $kladrId, int $limitInEachCategory = 5): array
    {
        return $this->repository->getOffersGroupedByCategoryIdAndKladrId($categoryIds, $kladrId, $limitInEachCategory);
    }

    /**
     * @param array<int> $kaisProductIds
     *
     * @return array{'totalCount': int, 'offers': ArrayCollection<int, Offer>}
     */
    #[Route('/offers/get-substitutes-by-kais-product-ids-and-kladr-id', name: 'offers-get-substitutes-by-kais-product-ids-and-kladr-id')]
    #[ParamConverter('kaisProductIds', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('kladrId', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('limit', options: ['default' => 1000], isOptional: true, converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('offset', options: ['default' => 0], isOptional: true, converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('sortBy', options: ['default' => null], isOptional: true, converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('sortOrder', options: ['default' => 'ASC'], isOptional: true, converter: 'json.request_body_param_fetcher')]
    public function getSubstitutesByKaisProductIdsAndKladrId(array $kaisProductIds, string $kladrId, int $limit = 10, int $offset = 0, ?string $sortBy = null, string $sortOrder = 'asc'): array
    {
        $substitutesAssortmentUnitIds = $this->kaisProductsDao->fetchSubstitutesAssortmentUnitIds($kaisProductIds);

        if (empty($substitutesAssortmentUnitIds)) {
            return [
                'totalCount' => 0,
                'offers'     => new ArrayCollection(),
            ];
        }

        return $this->repository->getOffers(kladrId: $kladrId, assortmentUnitIds: $substitutesAssortmentUnitIds, limit: $limit, offset: $offset, sortBy: $sortBy, sortOrder: $sortOrder);
    }

    /**
     * @return array{'totalCount': int, 'offers': ArrayCollection<int, Offer>, 'tradeName': string}
     */
    #[Route('/offers/get-analogs-by-transliterated-trade-name', name: 'offers-get-analogs-by-transliterated-trade-name')]
    #[ParamConverter('transliteratedTradeName', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('kladrId', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('limit', options: ['default' => 1000], isOptional: true, converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('offset', options: ['default' => 0], isOptional: true, converter: 'json.request_body_param_fetcher')]
    public function getAnalogsByTransliteratedTradeNameAndKladrId(string $transliteratedTradeName, string $kladrId, int $limit = 10, int $offset = 0): array
    {
        $analogsAssortmentUnitIds          = $this->kaisProductsDao->fetchAnalogsAssortmentUnitIdsByTransliteratedTradeName($transliteratedTradeName);
        $tradeName                         = $this->kaisProductsDao->fetchFullTradeNameByTransliteratedTradeName($transliteratedTradeName);
        $offersWithTotalCount              = $this->repository->getOffers(kladrId: $kladrId, assortmentUnitIds: $analogsAssortmentUnitIds, limit: $limit, offset: $offset);
        $offersWithTotalCount['tradeName'] = $tradeName;

        return $offersWithTotalCount;
    }

    /**
     * @return array{'totalCount': int, 'offers': ArrayCollection<int, Offer>, 'drugform': array{'productName': string, 'indicatorForUse': string, 'contraindicationsForUse': string, 'sideEffect': string}}
     */
    #[Route('/offers/get-all-drugforms-by-trade-name', name: 'offers-get-all-drugforms-by-trade-name')]
    #[ParamConverter('tradeName', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('kladrId', converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('limit', options: ['default' => 1000], isOptional: true, converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('offset', options: ['default' => 0], isOptional: true, converter: 'json.request_body_param_fetcher')]
    public function getAllDrugformsByTradeNameAndKladrId(string $tradeName, ?string $kladrId = null, int $limit = 1000, int $offset = 0): array
    {
        $assortmentUnitIds                = $this->kaisProductsDao->fetchAssortmentUnitIdsByTradeName($tradeName);
        $drugform                         = $this->kaisProductsDao->fetchAssortmentUnitDescriptionByTradeName($tradeName);
        $offersWithTotalCount             = $this->repository->getOffers(kladrId: $kladrId, assortmentUnitIds: $assortmentUnitIds, limit: $limit, offset: $offset);
        $offersWithTotalCount['drugform'] = $drugform;

        return $offersWithTotalCount;
    }
}
