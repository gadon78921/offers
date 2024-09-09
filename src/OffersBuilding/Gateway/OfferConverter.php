<?php

declare(strict_types=1);

namespace App\OffersBuilding\Gateway;

use App\OffersBuilding\Domain\Entity\Offer;
use MartinGeorgiev\Utils\DataStructure;
use Psr\Log\LoggerInterface;

final class OfferConverter
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {}

    public function toPostgresValuesForInsert(Offer $offer): string
    {
        try {
            $kaisProductId        = $offer->kaisProductId();
            $assortmentUnitId     = $offer->assortmentUnitId();
            $kladrId              = $offer->kladrId();
            $offerName            = $offer->name();
            $priceWithoutDiscount = $offer->priceWithoutDiscount();
            $priceForPreorder     = $offer->priceForPreorder();
            $priceForWaiting      = $offer->priceForWaiting();
            $discountForPreorder  = $offer->discountForPreorder();
            $discountForWaiting   = $offer->discountForWaiting();
            $wholesalePrice       = $offer->wholesalePrice();
            $categoryIds          = '\'' . DataStructure::transformPHPArrayToPostgresTextArray($offer->categoryIds()) . '\'';

            foreach ($offer->availableTradePoints() as $tradePoint) {
                $tradePointId = $tradePoint->id;
                $quantities   = $offer->getOfferQuantitiesInTradePoints($tradePointId);

                $offerRow['kaisProductId']             = $kaisProductId;
                $offerRow['assortmentUnitId']          = $assortmentUnitId;
                $offerRow['name']                      = '\'' . str_replace('\'', '\'\'', $offerName) . '\'';
                $offerRow['tradePointId']              = $tradePointId;
                $offerRow['kladrId']                   = $kladrId;
                $offerRow['priceWithoutDiscount']      = $priceWithoutDiscount;
                $offerRow['priceForPreorder']          = $priceForPreorder;
                $offerRow['priceForWaiting']           = $priceForWaiting;
                $offerRow['discountForPreorder']       = $discountForPreorder;
                $offerRow['discountForWaiting']        = $discountForWaiting;
                $offerRow['quantityInStorage']         = $quantities['quantity'];
                $offerRow['quantityInStorageUnpacked'] = $quantities['quantityUnpacked'];
                $offerRow['quantityFromSuppliers']     = $offer->quantityFromSuppliersForTradePoint($tradePointId);
                $offerRow['supplierIds']               = '\'' . DataStructure::transformPHPArrayToPostgresTextArray($offer->supplierIdsForTradePoint($tradePointId)->getKeys()) . '\'';
                $offerRow['wholesalePrice']            = $wholesalePrice;
                $offerRow['categoryIds']               = $categoryIds;

                $valuesForInsert[] = '(' . implode(',', $offerRow) . ')';
            }
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage());
            $this->logger->error((string) $offer->assortmentUnitId());
        }

        return implode(',', $valuesForInsert ?? []);
    }
}
