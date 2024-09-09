<?php

declare(strict_types=1);

namespace App\OffersBuilding\Services;

use App\OffersBuilding\Gateway\OffersForSiteGateway;
use App\OffersBuilding\Gateway\ProductGateway;
use App\OffersBuilding\Repository\OffersForSite\OfferByAssortmentUnitIdRepository;
use App\OffersBuilding\Repository\OffersForSite\OfferByKaisProductIdRepository;
use App\OffersBuilding\Repository\OffersFromSuppliers\ProductsFromSuppliersRepository;
use App\OffersBuilding\Repository\OffersFromTradePoints\ProductsInTradePointsRepository;
use App\OffersBuilding\Repository\TradePoints\TradePointsRepository;
use App\OffersBuilding\Repository\WebPricing\WebPricingRepository;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

final class BuildOffersByTradePointService
{
    public function __construct(
        private readonly Connection $connection,
        private readonly TradePointsRepository $tradePointRepository,
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
     * @param array<int> $tradePointIds
     * @param array<int> $assortmentUnitIds
     * @param array<int> $kaisProductIds
     */
    public function build(array $tradePointIds, array $assortmentUnitIds = [], array $kaisProductIds = []): void
    {
        $this->logger->info('Start build');
        foreach ($tradePointIds as $tradePointId) {
            $tradePoint = $this->tradePointRepository->getByTradePointId((int) $tradePointId);

            if (null === $tradePoint) {
                continue;
            }

            $tradePointIdsByKladrId[$tradePoint->kladrId][] = (int) $tradePointId;
        }

        foreach ($tradePointIdsByKladrId ?? [] as $kladrId => $tradePoints) {
            $this->buildByTradepoint((string) $kladrId, $tradePoints, $assortmentUnitIds, $kaisProductIds);
        }
        $this->logger->info('Finish build');
    }

    /**
     * @param array<int> $tradePointIds
     * @param array<int> $assortmentUnitIds
     * @param array<int> $kaisProductIds
     */
    private function buildByTradepoint(string $kladrId, array $tradePointIds, array $assortmentUnitIds = [], array $kaisProductIds = []): void
    {
        $this->logger->info('Start build tradePointIds: ' . json_encode($tradePointIds) . ', assortmentUnitIds: ' . json_encode($assortmentUnitIds) . ', kaisProductIds: ' . json_encode($kaisProductIds));

        $this->productsInTradePointsRepository->fillByTradePointIds($tradePointIds, $assortmentUnitIds, $kaisProductIds);
        $this->productsFromSuppliersRepository->fillByTradePointIds($tradePointIds, $assortmentUnitIds, $kaisProductIds);
        $this->webPricingRepository->fillByKladrId($kladrId);
        $this->offerByAssortmentUnitIdRepository->fillFromSourceRepositories();
        $this->offerByKaisProductIdRepository->fillFromSourceRepositories();
        $this->clearSourceRepositories();

        $this->connection->beginTransaction();
        try {
            if (empty($assortmentUnitIds) && empty($kaisProductIds)) {
                $this->offerGateway->removeByTradePointIds($tradePointIds);
                $this->productGateway->removeByTradePointIds($tradePointIds);
            }

            if (!empty($assortmentUnitIds) && empty($kaisProductIds)) {
                $this->offerGateway->removeByAssortmentUnitIdsAndTradePointIds($assortmentUnitIds, $tradePointIds);
                $this->productGateway->removeByAssortmentUnitIdsAndTradePointIds($assortmentUnitIds, $tradePointIds);
            }

            if (empty($assortmentUnitIds) && !empty($kaisProductIds)) {
                $this->offerGateway->removeByKaisProductIdsAndTradePointIds($kaisProductIds, $tradePointIds);
                $this->productGateway->removeByKaisProductIdsAndTradePointIds($kaisProductIds, $tradePointIds);
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

        $this->logger->info('Finish build tradePointIds');
    }

    public function clearSourceRepositories(): void
    {
        $this->productsInTradePointsRepository->clear();
        $this->productsFromSuppliersRepository->clear();
        $this->webPricingRepository->clear();
    }

    public function clearOfferRepositories(): void
    {
        $this->offerByAssortmentUnitIdRepository->clear();
        $this->offerByKaisProductIdRepository->clear();
    }
}
