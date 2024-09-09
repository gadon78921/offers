<?php

declare(strict_types=1);

namespace App\OffersApi\Controller;

use App\OffersApi\Domain\Entity\Offer\Offer;
use App\OffersApi\Repository\Favorites\FavoritesDatabaseAccessObject;
use App\OffersApi\Repository\Offer\OfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class FavoritesController extends AbstractController
{
    public function __construct(
        private readonly OfferRepository $offerRepository,
        private readonly FavoritesDatabaseAccessObject $favoritesDao,
    ) {}

    /**
     * @return array{'totalCount': int, 'offers': ArrayCollection<int, Offer>}
     */
    #[Route('/favorite-offers/get-by-client-id-and-kladr-id')]
    #[ParamConverter('clientId', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('kladrId', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('limit', options: ['default' => 1000], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('offset', options: ['default' => 0], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('sortBy', options: ['default' => null], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('sortOrder', options: ['default' => 'ASC'], converter: 'json.request_body_param_fetcher')]
    public function getByClientIdAndKladrId(int $clientId, string $kladrId, int $limit, int $offset, ?string $sortBy, string $sortOrder): array
    {
        $favoritesAssortmentUnitIds = $this->favoritesDao->fetchByClientId($clientId);

        return $this->offerRepository->getOffers(kladrId: $kladrId, assortmentUnitIds: $favoritesAssortmentUnitIds, limit: $limit, offset: $offset, sortBy: $sortBy, sortOrder: $sortOrder);
    }

    /**
     * @return list<int>
     */
    #[Route('/favorite-offers/get-only-assortment-unit-ids-by-client-id')]
    #[ParamConverter('clientId', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    public function getOnlyAssortmentUnitIdsByClientId(int $clientId): array
    {
        return $this->favoritesDao->fetchByClientId($clientId);
    }

    #[Route('/favorite-offers/add')]
    #[ParamConverter('clientId', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('assortmentUnitId', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    public function add(int $clientId, int $assortmentUnitId): void
    {
        $this->favoritesDao->add($clientId, $assortmentUnitId);
    }

    #[Route('/favorite-offers/remove')]
    #[ParamConverter('clientId', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('assortmentUnitId', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    public function remove(int $clientId, int $assortmentUnitId): void
    {
        $this->favoritesDao->remove($clientId, $assortmentUnitId);
    }
}
