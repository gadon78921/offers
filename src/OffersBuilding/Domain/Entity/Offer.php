<?php

declare(strict_types=1);

namespace App\OffersBuilding\Domain\Entity;

use App\OffersBuilding\Domain\ValueObject\WebPricing;
use Doctrine\Common\Collections\ArrayCollection;

final class Offer
{
    private const MAX_QUANTITY = 99;

    private ?WebPricing $priceFromWebPricing = null;

    /** @var ArrayCollection<int, ProductInTradePoint> */
    private ArrayCollection $productInTradePointCollection;

    /** @var ArrayCollection<int, ProductFromSupplier> */
    private ArrayCollection $productFromSupplierCollection;

    /** @var array<int> */
    private array $categoryIds;

    public function __construct(
        private readonly int $kaisProductId,
        private readonly int $assortmentUnitId,
        private readonly string $kladrId,
        private readonly string $name,
    ) {
        $this->productInTradePointCollection = new ArrayCollection();
        $this->productFromSupplierCollection = new ArrayCollection();
    }

    public function kaisProductId(): int
    {
        return $this->kaisProductId;
    }

    public function assortmentUnitId(): int
    {
        return $this->assortmentUnitId;
    }

    public function kladrId(): string
    {
        return $this->kladrId;
    }

    public function name(): string
    {
        return $this->name;
    }

    /** @return ArrayCollection<int, TradePoint> */
    public function availableTradePoints(): ArrayCollection
    {
        $collection = new ArrayCollection();

        $this->productInTradePointCollection->map(fn(ProductInTradePoint $offer) => $collection->set($offer->tradePoint->id, $offer->tradePoint));

        $this->productFromSupplierCollection->map(function (ProductFromSupplier $offer) use ($collection) {
            $offer->tradePoints->map(fn(TradePoint $tradePoint) => $collection->set($tradePoint->id, $tradePoint));
        });

        return $collection;
    }

    public function setPriceFromWebPricing(?WebPricing $priceFromWebPricing): void
    {
        $this->priceFromWebPricing = $priceFromWebPricing;
    }

    public function addProductInTradePoint(ProductInTradePoint $tradePointOffer): void
    {
        $this->productInTradePointCollection->add($tradePointOffer);
    }

    public function addSupplierOffer(ProductFromSupplier $supplierOffer): void
    {
        $this->productFromSupplierCollection->set($supplierOffer->supplierId, $supplierOffer);
    }

    /** @return array{'quantity': int<min, 99>, 'quantityUnpacked': int<min, 99>} */
    public function getOfferQuantitiesInTradePoints(int $tradePointId): array
    {
        $tradePointOffers = $this->productInTradePointCollection->filter(fn(ProductInTradePoint $offer) => $offer->tradePoint->id === $tradePointId);

        foreach ($tradePointOffers as $tradePointOffer) {
            $result['quantity'][]         = $tradePointOffer->quantity;
            $result['quantityUnpacked'][] = $tradePointOffer->quantityUnpacked;
        }

        $quantity         = min(array_sum($result['quantity'] ?? [0]), self::MAX_QUANTITY);
        $quantityUnpacked = min(array_sum($result['quantityUnpacked'] ?? [0]), self::MAX_QUANTITY);

        return [
            'quantity'         => $quantity,
            'quantityUnpacked' => $quantityUnpacked,
        ];
    }

    public function quantityFromSuppliersForTradePoint(int $tradePointId): int
    {
        $quantities = $this->productFromSupplierCollection->map(function (ProductFromSupplier $offer) use ($tradePointId) {
            $tradePointExist = $offer->tradePoints->exists(fn($key, TradePoint $tradePoint) => $tradePoint->id === $tradePointId);

            return $tradePointExist ? $offer->quantity : 0;
        });

        return min(array_sum($quantities->toArray()), self::MAX_QUANTITY);
    }

    /** @return ArrayCollection<int, null> */
    public function supplierIdsForTradePoint(int $tradePointId): ArrayCollection
    {
        $supplierIdsForTradePoint = new ArrayCollection();
        $this->productFromSupplierCollection->forAll(function ($key, ProductFromSupplier $offer) use (&$supplierIdsForTradePoint, $tradePointId) {
            $tradePointExist = $offer->tradePoints->exists(fn($key, TradePoint $tradePoint) => $tradePoint->id === $tradePointId);

            if ($tradePointExist) {
                $supplierIdsForTradePoint->set($offer->supplierId, null);
            }

            return true;
        });

        return $supplierIdsForTradePoint;
    }

    public function priceWithoutDiscount(): float
    {
        $price = $this->priceFromWebPricing?->priceWithoutDiscount;
        $price ??= ($this->priceForPreorder() / (1 - ($this->discountForPreorder() / 100)));

        return round($price, 2);
    }

    public function priceForPreorder(): float
    {
        return $this->priceFromWebPricing?->priceForPreorder ?? $this->defaultPriceWithDiscount();
    }

    public function priceForWaiting(): float
    {
        return $this->priceFromWebPricing?->priceForWaiting ?? $this->defaultPriceWithDiscount();
    }

    public function discountForPreorder(): int
    {
        return $this->priceFromWebPricing?->discountForPreorder ?? $this->defaultDiscount();
    }

    public function discountForWaiting(): int
    {
        return $this->priceFromWebPricing?->discountForWaiting ?? $this->defaultDiscount();
    }

    public function wholesalePrice(): float
    {
        $wholesalePriceCollection = $this->productInTradePointCollection->map(fn(ProductInTradePoint $productInTradePoint) => $productInTradePoint->wholesalePrice);
        $wholesalePrices          = $wholesalePriceCollection->getValues();
        $counts                   = count($wholesalePrices);

        return 0 === $counts ? 0.0 : array_sum($wholesalePrices) / count($wholesalePrices);
    }

    /** @return array<int> $categoryIds */
    public function categoryIds(): array
    {
        return $this->categoryIds;
    }

    /** @param array<int> $categoryIds */
    public function setCategoryIds(array $categoryIds): void
    {
        $this->categoryIds = $categoryIds;
    }

    private function defaultPriceWithDiscount(): float
    {
        return 0.0;
    }

    private function defaultDiscount(): int
    {
        return 0;
    }
}
