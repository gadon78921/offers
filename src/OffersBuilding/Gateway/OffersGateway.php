<?php

declare(strict_types=1);

namespace App\OffersBuilding\Gateway;

use App\OffersBuilding\Domain\Entity\Offer;
use App\OffersBuilding\Repository\OffersForSite\OfferRepository;
use Psr\Log\LoggerInterface;

abstract class OffersGateway
{
    public function __construct(
        protected readonly OfferRepository $repository,
        protected readonly DatabaseAccessObject $dao,
        protected readonly OfferConverter $converter,
        protected readonly LoggerInterface $logger,
    ) {}

    public function save(): void
    {
        $offersToInsert = $this->prepareToInsert();
        $this->dao->insert($offersToInsert);
    }

    /** @param array<int> $assortmentUnitIds */
    public function removeByAssortmentUnitIds(array $assortmentUnitIds): void
    {
        $this->dao->removeByAssortmentUnitIds($assortmentUnitIds);
    }

    /** @param array<int> $tradePointIds */
    public function removeByTradePointIds(array $tradePointIds): void
    {
        $this->dao->removeByTradePointIds($tradePointIds);
    }

    public function removeByKladrId(string $kladrId): void
    {
        $this->dao->removeByKladrId($kladrId);
    }

    /** @param array<int> $assortmentUnitIds */
    public function removeByAssortmentUnitIdsAndKladrId(array $assortmentUnitIds, string $kladrId): void
    {
        $this->dao->removeByAssortmentUnitIdsAndKladrId($assortmentUnitIds, $kladrId);
    }

    /** @param array<int> $kaisProductIds */
    public function removeByKaisProductIdsAndKladrId(array $kaisProductIds, string $kladrId): void
    {
        $this->dao->removeByKaisProductIdsAndKladrId($kaisProductIds, $kladrId);
    }

    /**
     * @param array<int> $assortmentUnitIds
     * @param array<int> $tradePointIds
     */
    public function removeByAssortmentUnitIdsAndTradePointIds(array $assortmentUnitIds, array $tradePointIds): void
    {
        $this->dao->removeByAssortmentUnitIdsAndTradePointIds($assortmentUnitIds, $tradePointIds);
    }

    /**
     * @param array<int> $kaisProductIds
     * @param array<int> $tradePointIds
     */
    public function removeByKaisProductIdsAndTradePointIds(array $kaisProductIds, array $tradePointIds): void
    {
        $this->dao->removeByKaisProductIdsAndTradePointIds($kaisProductIds, $tradePointIds);
    }

    /** @return array<int, string> */
    private function prepareToInsert(): array
    {
        $availableOfferCollection = $this->repository->filter(fn(Offer $offer) => $offer->priceForPreorder() > 0);
        $availableOfferCollection = $availableOfferCollection->map(fn(Offer $offer) => $this->converter->toPostgresValuesForInsert($offer));

        return $availableOfferCollection->toArray();
    }
}
