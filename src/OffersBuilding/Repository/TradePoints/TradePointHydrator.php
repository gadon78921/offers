<?php

declare(strict_types=1);

namespace App\OffersBuilding\Repository\TradePoints;

use App\OffersBuilding\Domain\Entity\TradePoint;

final class TradePointHydrator
{
    /** @param array{'tradePointId': int, 'kladrId': string, 'deliveryAvailable': bool, 'firmIds': array{int}, 'supplierIds': array{int}} $tradePointData */
    public function hydrateTradePoint(array $tradePointData): TradePoint
    {
        return new TradePoint(
            (int) $tradePointData['tradePointId'],
            (string) $tradePointData['kladrId'],
            (bool) $tradePointData['deliveryAvailable'],
            (array) $tradePointData['firmIds'],
            (array) $tradePointData['supplierIds'],
        );
    }
}
