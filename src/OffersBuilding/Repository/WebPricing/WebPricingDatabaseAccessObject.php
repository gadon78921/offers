<?php

declare(strict_types=1);

namespace App\OffersBuilding\Repository\WebPricing;

use Doctrine\DBAL\Connection;
use MartinGeorgiev\Utils\DataStructure;

final class WebPricingDatabaseAccessObject
{
    public function __construct(
        private readonly Connection $connection,
    ) {}

    /**
     * @param array{int}|array{} $assortmentUnitIds
     *
     * @return \Traversable<int, array{'assortmentUnitId': int, 'priceWithoutDiscount': float, 'priceForPreorder': float, 'priceForWaiting': float, 'discountForPreorder': int, 'discountForWaiting': int, 'isFixedDiscount': bool}>
     */
    public function fetchWebPricing(string $kladrId, array $assortmentUnitIds = []): \Traversable
    {
        $sql = '
            SELECT assortment_unit_id    as "assortmentUnitId",
                   price                 as "priceWithoutDiscount",
                   price_for_preorder    as "priceForPreorder",
                   price_for_waiting     as "priceForWaiting",
                   discount_for_preorder as "discountForPreorder",
                   discount_for_waiting  as "discountForWaiting",
                   is_fixed_discount     as "isFixedDiscount"
            FROM retail_prices
            WHERE kladr_id = \'' . $kladrId . '\'
        ';

        $sql .= empty($assortmentUnitIds) ? '' : ' AND assortment_unit_id = ANY(:assortmentUnitIds)';

        return $this->connection->executeQuery($sql, ['assortmentUnitIds' => DataStructure::transformPHPArrayToPostgresTextArray($assortmentUnitIds)])->iterateAssociative();
    }
}
