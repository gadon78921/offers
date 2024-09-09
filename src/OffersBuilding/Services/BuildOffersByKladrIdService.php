<?php

declare(strict_types=1);

namespace App\OffersBuilding\Services;

use App\OffersBuilding\Gateway\OffersForSiteGateway;
use App\OffersBuilding\Gateway\ProductGateway;
use App\OffersBuilding\Repository\Kladr\KladrRepository;
use App\OffersBuilding\Repository\OffersForSite\OfferByAssortmentUnitIdRepository;
use App\OffersBuilding\Repository\OffersForSite\OfferByKaisProductIdRepository;
use App\OffersBuilding\Repository\OffersFromSuppliers\ProductsFromSuppliersRepository;
use App\OffersBuilding\Repository\OffersFromTradePoints\ProductsInTradePointsRepository;
use App\OffersBuilding\Repository\WebPricing\WebPricingRepository;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

final class BuildOffersByKladrIdService
{
    public function __construct(
        private readonly Connection $connection,
        private readonly KladrRepository $kladrRepository,
        private readonly ProductsInTradePointsRepository $productsInTradePointsRepository,
        private readonly ProductsFromSuppliersRepository $productsFromSuppliersRepository,
        private readonly WebPricingRepository $webPricingRepository,
        private readonly OfferByAssortmentUnitIdRepository $offerByAssortmentUnitIdRepository,
        private readonly OfferByKaisProductIdRepository $offerByKaisProductIdRepository,
        private readonly OffersForSiteGateway $offerGateway,
        private readonly ProductGateway $productGateway,
        protected LoggerInterface $logger,
    ) {}

    /**
     * @param array<int, string> $kladrIds
     * @param array<int>         $assortmentUnitIds
     * @param array<int>         $kaisProductIds
     */
    public function build(array $kladrIds = [], array $assortmentUnitIds = [], array $kaisProductIds = []): void
    {
        $this->logger->info('Start build');
        $kladrIds = empty($kladrIds) ? $this->kladrRepository->getCollection()->toArray() : $kladrIds;

        foreach ($kladrIds as $kladrId) {
            $this->buildByKladrId($kladrId, $assortmentUnitIds, $kaisProductIds);
        }

        $this->logger->info('Finish build');
    }

    /**
     * @param array<int> $assortmentUnitIds
     * @param array<int> $kaisProductIds
     */
    private function buildByKladrId(string $kladrId, array $assortmentUnitIds = [], array $kaisProductIds = []): void
    {
        $this->logger->info('Start build kladr: ' . $kladrId . ', assortmentUnitIds: ' . json_encode($assortmentUnitIds) . ', kaisProductIds: ' . json_encode($kaisProductIds));

        $this->productsInTradePointsRepository->fillByKladrId($kladrId, $assortmentUnitIds, $kaisProductIds);
        $this->productsFromSuppliersRepository->fillByKladrId($kladrId, $assortmentUnitIds, $kaisProductIds);
        $this->webPricingRepository->fillByKladrId($kladrId, $assortmentUnitIds);
        $this->offerByAssortmentUnitIdRepository->fillFromSourceRepositories();
        $this->offerByKaisProductIdRepository->fillFromSourceRepositories();
        $this->clearSourceRepositories();

        $this->connection->beginTransaction();
        try {
            if (empty($assortmentUnitIds) && empty($kaisProductIds)) {
                $this->offerGateway->removeByKladrId($kladrId);
                $this->productGateway->removeByKladrId($kladrId);
            }

            if (!empty($assortmentUnitIds) && empty($kaisProductIds)) {
                $this->offerGateway->removeByAssortmentUnitIdsAndKladrId($assortmentUnitIds, $kladrId);
                $this->productGateway->removeByAssortmentUnitIdsAndKladrId($assortmentUnitIds, $kladrId);
            }

            if (empty($assortmentUnitIds) && !empty($kaisProductIds)) {
                $this->offerGateway->removeByKaisProductIdsAndKladrId($kaisProductIds, $kladrId);
                $this->productGateway->removeByKaisProductIdsAndKladrId($kaisProductIds, $kladrId);
            }

            $this->offerGateway->save();
            $this->productGateway->save();
            $this->connection->commit();
        } catch (\Throwable $exception) {
            $this->connection->rollBack();
            $this->logger->info($exception->getMessage());
            throw $exception;
        }

        $this->clearOfferRepositories();

        $this->logger->info('Finish build kladr');
    }

    private function clearSourceRepositories(): void
    {
        $this->productsInTradePointsRepository->clear();
        $this->productsFromSuppliersRepository->clear();
        $this->webPricingRepository->clear();
    }

    private function clearOfferRepositories(): void
    {
        $this->offerByAssortmentUnitIdRepository->clear();
        $this->offerByKaisProductIdRepository->clear();
    }
}
