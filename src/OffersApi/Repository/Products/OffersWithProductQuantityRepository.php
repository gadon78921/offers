<?php

declare(strict_types=1);

namespace App\OffersApi\Repository\Products;

use App\OffersApi\Domain\Entity\OfferWithProductQuantity\OfferWithProductsQuantity;
use App\OffersApi\Domain\Entity\OfferWithProductQuantity\ProductsQuantity;
use App\OffersApi\Domain\Entity\OfferWithProductQuantity\QuantityFromSupplier;
use App\OffersApi\Domain\Entity\OfferWithProductQuantity\QuantityProduct;
use App\OffersApi\Repository\SuppliersForTradePoints\SuppliersForTradePointsRepository;
use Doctrine\Common\Collections\ArrayCollection;

final class OffersWithProductQuantityRepository
{
    public function __construct(
        private readonly ProductsDatabaseAccessObject $dao,
        private readonly SuppliersForTradePointsRepository $suppliersForTradePointsRepository,
    ) {}

    /**
     * @param array<int> $assortmentUnitIds
     *
     * @return ArrayCollection<int, OfferWithProductsQuantity>
     */
    public function getByKladrIdAndAssortmentUnitIds(string $kladrId, array $assortmentUnitIds): ArrayCollection
    {
        $collection = new ArrayCollection();

        foreach ($this->dao->fetch($kladrId, $assortmentUnitIds) as $productData) {
            $offerWithProductsInTradePoints = new OfferWithProductsQuantity($productData['assortmentUnitId'], new ArrayCollection());
            $this->createAndAddProductsInTradePointToOfferWithProductsInTradePoints($productData, $offerWithProductsInTradePoints);

            $collection->set($offerWithProductsInTradePoints->assortmentUnitId, $offerWithProductsInTradePoints);
        }

        return $collection;
    }

    /** @param array{'productsInTradePoints': string, 'productsFromSupplier': string} $productData */
    private function createAndAddProductsInTradePointToOfferWithProductsInTradePoints(array $productData, OfferWithProductsQuantity $offerWithProductsInTradePoints): void
    {
        $productsInTradePointsRaw = json_decode($productData['productsInTradePoints'], true, 512, JSON_THROW_ON_ERROR);
        $productsFromSuppliers    = json_decode($productData['productsFromSupplier'], true, 512, JSON_THROW_ON_ERROR);

        foreach ($productsInTradePointsRaw as $productInTradePointRaw) {
            $productsQuantity = $offerWithProductsInTradePoints->productsQuantity->get($productInTradePointRaw['tradePointId']);

            if (null === $productsQuantity) {
                $productsQuantity = new ProductsQuantity($productInTradePointRaw['tradePointId'], new ArrayCollection(), new ArrayCollection());
            }

            $quantityProductInTradePoint = new QuantityProduct(
                $productInTradePointRaw['retailProductId'],
                $productInTradePointRaw['quantityInStorage'],
                $productInTradePointRaw['quantityInStorageUnpacked']
            );

            $this->processProductFromSuppliers($productsFromSuppliers, $productsQuantity, $productInTradePointRaw['retailProductId']);

            $productsQuantity->quantityInTradePoint->add($quantityProductInTradePoint);
            $offerWithProductsInTradePoints->productsQuantity->set($productInTradePointRaw['tradePointId'], $productsQuantity);
        }
    }

    /** @param array<int, array{'supplierId': int, 'quantityFromSupplier': int, 'retailProductId': int, 'cost': float}> $productsFromSuppliersRaw */
    private function processProductFromSuppliers(array $productsFromSuppliersRaw, ProductsQuantity $productsInTradePoint, int $retailProductId): void
    {
        usort($productsFromSuppliersRaw, function (array $productFromSuppliersRaw1, array $productFromSuppliersRaw2) {
            return $productFromSuppliersRaw1['cost'] <=> $productFromSuppliersRaw2['cost'];
        });

        foreach ($productsFromSuppliersRaw as $productFromSuppliersRaw) {
            if (false === $this->isSupplierWorkWithTradePoint($productFromSuppliersRaw['supplierId'], $productsInTradePoint->tradePointId)) {
                continue;
            }

            if (0 === $productFromSuppliersRaw['quantityFromSupplier']) {
                continue;
            }

            if ($productFromSuppliersRaw['retailProductId'] !== $retailProductId) {
                continue;
            }

            $quantityProductFromSupplier = $productsInTradePoint->quantityFromSuppliers->get($productFromSuppliersRaw['supplierId']);

            if (null === $quantityProductFromSupplier) {
                $quantityProductFromSupplier = new QuantityFromSupplier($productFromSuppliersRaw['supplierId'], new ArrayCollection());
            }

            $quantity = new QuantityProduct($productFromSuppliersRaw['retailProductId'], $productFromSuppliersRaw['quantityFromSupplier'], 0);
            $quantityProductFromSupplier->quantity->add($quantity);

            $productsInTradePoint->quantityFromSuppliers->set($productFromSuppliersRaw['supplierId'], $quantityProductFromSupplier);
        }
    }

    private function isSupplierWorkWithTradePoint(?int $supplierId, int $tradePointId): bool
    {
        if (null === $supplierId) {
            return false;
        }

        $suppliersForTradePoint = $this->suppliersForTradePointsRepository->get($tradePointId);

        return null !== $suppliersForTradePoint && in_array($supplierId, $suppliersForTradePoint->supplierIds, true);
    }
}
