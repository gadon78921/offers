<?php

declare(strict_types=1);

namespace App\OffersBuilding\Repository\OffersForSite;

use App\OffersBuilding\Domain\Entity\Offer;
use App\OffersBuilding\Domain\Entity\ProductFromSupplier;
use App\OffersBuilding\Domain\Entity\ProductInTradePoint;
use App\OffersBuilding\Repository\Categories\CategoryRepository;
use App\OffersBuilding\Repository\OffersFromSuppliers\ProductsFromSuppliersRepository;
use App\OffersBuilding\Repository\OffersFromTradePoints\ProductsInTradePointsRepository;
use App\OffersBuilding\Repository\WebPricing\WebPricingRepository;
use Doctrine\Common\Collections\ArrayCollection;

abstract class OfferRepository
{
    /** @var ArrayCollection<int, Offer> */
    protected ArrayCollection $offerByAssortmentUnitIdsCollection;

    public function __construct(
        private readonly ProductsInTradePointsRepository $productsInTradePointsRepository,
        private readonly ProductsFromSuppliersRepository $productsFromSuppliersRepository,
        private readonly WebPricingRepository $webPricingRepository,
        private readonly CategoryRepository $categoryRepository,
    ) {
        $this->offerByAssortmentUnitIdsCollection = new ArrayCollection();
    }

    public function clear(): void
    {
        $this->offerByAssortmentUnitIdsCollection = new ArrayCollection();
    }

    /** @return ArrayCollection<int, Offer> */
    public function filter(\Closure $func): ArrayCollection
    {
        return $this->offerByAssortmentUnitIdsCollection->filter($func);
    }

    public function fillFromSourceRepositories(): void
    {
        $this->createFromProductsInTradePointsRepository();
        $this->createFromProductFromSuppliersRepository();
        $this->addCategoryIds();
    }

    private function addCategoryIds(): void
    {
        $assortmentUnitIds = [];
        $this->offerByAssortmentUnitIdsCollection->forAll(function (int $index, Offer $offer) use (&$assortmentUnitIds) {
            $assortmentUnitIds[] = $offer->assortmentUnitId();

            return true;
        });

        if (!empty($assortmentUnitIds)) {
            $assortmentUnitIds = array_unique($assortmentUnitIds);
            $this->categoryRepository->fill($assortmentUnitIds);
            $this->offerByAssortmentUnitIdsCollection->forAll(function (int $index, Offer $offer) {
                $offer->setCategoryIds($this->categoryRepository->getByAssortmentUnitId($offer->assortmentUnitId()));

                return true;
            });
        }
    }

    private function createFromProductsInTradePointsRepository(): void
    {
        $this->productsInTradePointsRepository->forAll(function ($key, ProductInTradePoint $tradePointOffer) {
            $kaisProductId    = $tradePointOffer->kaisProductId;
            $assortmentUnitId = $tradePointOffer->assortmentUnitId;
            $kladrId          = $tradePointOffer->tradePoint->kladrId;
            $productName      = $tradePointOffer->productName;
            $offer            = $this->getOrCreateOffer($kaisProductId, $assortmentUnitId, $kladrId, $productName);

            $offer->addProductInTradePoint($tradePointOffer);
            $this->setOfferInCollection($offer);

            return true;
        });
    }

    private function createFromProductFromSuppliersRepository(): void
    {
        $this->productsFromSuppliersRepository->forAll(function ($key, ProductFromSupplier $supplierOffer) {
            $kaisProductId    = $supplierOffer->kaisProductId;
            $assortmentUnitId = $supplierOffer->assortmentUnitId;
            $kladrId          = $supplierOffer->tradePoints->first()->kladrId;
            $productName      = $supplierOffer->productName;
            $offer            = $this->getOrCreateOffer($kaisProductId, $assortmentUnitId, $kladrId, $productName);

            $offer->addSupplierOffer($supplierOffer);
            $this->setOfferInCollection($offer);

            return true;
        });
    }

    private function getOrCreateOffer(int $kaisProductId, int $assortmentUnitId, string $kladrId, string $productName): Offer
    {
        $offer = new Offer($kaisProductId, $assortmentUnitId, $kladrId, $productName);
        $offer = $this->getOfferFromCollection($offer) ?? $offer;
        $offer->setPriceFromWebPricing($this->webPricingRepository->getByAssortmentUnitId($assortmentUnitId));

        return $offer;
    }

    abstract protected function getOfferFromCollection(Offer $offer): ?Offer;

    abstract protected function setOfferInCollection(Offer $offer): void;
}
