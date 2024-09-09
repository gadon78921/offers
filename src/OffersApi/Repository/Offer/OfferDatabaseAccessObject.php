<?php

declare(strict_types=1);

namespace App\OffersApi\Repository\Offer;

use Doctrine\DBAL\Connection;
use MartinGeorgiev\Utils\DataStructure;

final class OfferDatabaseAccessObject
{
    private const SORT_MAPPING = [
        'price'     => '"priceForPreorder"',
        'name'      => 'name',
        'relevancy' => 'array_position(:assortmentUnitIds, assortment_unit_id)',
    ];

    public function __construct(
        private readonly Connection $connection,
    ) {}

    /**
     * @param array<int> $assortmentUnitIds
     *
     * @return \Traversable<int, array{'totalCount': int, 'assortmentUnitId': int, 'priceWithoutDiscount': float, 'priceForPreorder': float, 'priceForWaiting': float, 'discountForPreorder': int, 'discountForWaiting': int, 'wholesalePrice': float, 'availability': string}>
     */
    public function fetch(
        ?string $kladrId,
        array $assortmentUnitIds,
        int $limit = 1000,
        int $offset = 0,
        ?string $sortBy = null,
        string $sortOrder = 'ASC'
    ): \Traversable {
        $sql = <<<SQL
                SELECT
                    o.assortment_unit_id                as "assortmentUnitId",
                    MAX(o.price_without_discount)       as "priceWithoutDiscount",
                    MIN(o.price_for_preorder)           as "priceForPreorder",
                    MIN(o.price_for_waiting)            as "priceForWaiting",
                    MIN(o.discount_for_preorder)        as "discountForPreorder",
                    MIN(o.discount_for_waiting)         as "discountForWaiting",
                    MIN(o.wholesale_price)              as "wholesalePrice",
                    jsonb_agg(
                        json_build_object(
                            'tradePointId',              o.tradepoint_id,
                            'quantityInStorage',         o.quantity_in_storage,
                            'quantityInStorageUnpacked', o.quantity_in_storage_unpacked,
                            'quantityFromSuppliers',     o.quantity_from_suppliers
                        )
                    ) as "availability"
                FROM offers o
                WHERE true
            SQL;

        $sql .= null === $kladrId ? '' : ' AND o.kladr_id = :kladrId';
        $sql .= empty($assortmentUnitIds) ? '' : ' AND o.assortment_unit_id = ANY(:assortmentUnitIds)';
        $sql .= ' GROUP BY assortment_unit_id';
        $sql .= 'name' === $sortBy ? ', name' : '';

        $sortField = self::SORT_MAPPING[$sortBy] ?? null;
        $sortOrder = 'relevancy' === $sortBy ? 'asc' : $sortOrder;
        $sql .= null === $sortField ? '' : ' ORDER BY ' . $sortField . ' ' . $sortOrder;
        $sql .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;

        return $this->connection->executeQuery(
            $sql,
            [
                'kladrId'           => $kladrId,
                'assortmentUnitIds' => DataStructure::transformPHPArrayToPostgresTextArray($assortmentUnitIds),
            ]
        )->iterateAssociative();
    }

    /**
     * @param array<int> $assortmentUnitIds
     */
    public function fetchTotalCount(?string $kladrId, array $assortmentUnitIds): int
    {
        $sql = <<<SQL
                SELECT count(DISTINCT o.assortment_unit_id) as "totalCount"
                FROM offers o
                WHERE true
            SQL;

        $sql .= null === $kladrId ? '' : ' AND o.kladr_id = :kladrId';
        $sql .= empty($assortmentUnitIds) ? '' : ' AND o.assortment_unit_id = ANY(:assortmentUnitIds)';

        $result = $this->connection->executeQuery(
            $sql,
            [
                'kladrId'           => $kladrId,
                'assortmentUnitIds' => DataStructure::transformPHPArrayToPostgresTextArray($assortmentUnitIds),
            ]
        )->fetchOne();

        return (int) ($result ?? 0);
    }

    /**
     * @param array<int> $retailProductIds
     *
     * @return \Traversable<int, array{'totalCount': int, 'assortmentUnitId': int, 'priceWithoutDiscount': float, 'priceForPreorder': float, 'priceForWaiting': float, 'discountForPreorder': int, 'discountForWaiting': int, 'wholesalePrice': float, 'availability': string}>
     */
    public function fetchByRetailProductIdsAndKladrId(?string $kladrId, array $retailProductIds): \Traversable
    {
        $sql = <<<SQL
                SELECT
                    o.assortment_unit_id                as "assortmentUnitId",
                    MAX(o.price_without_discount)       as "priceWithoutDiscount",
                    MIN(o.price_for_preorder)           as "priceForPreorder",
                    MIN(o.price_for_waiting)            as "priceForWaiting",
                    MIN(o.discount_for_preorder)        as "discountForPreorder",
                    MIN(o.discount_for_waiting)         as "discountForWaiting",
                    MIN(o.wholesale_price)              as "wholesalePrice",
                    jsonb_agg(
                        json_build_object(
                            'tradePointId',              o.tradepoint_id,
                            'quantityInStorage',         o.quantity_in_storage,
                            'quantityInStorageUnpacked', o.quantity_in_storage_unpacked,
                            'quantityFromSuppliers',     o.quantity_from_suppliers
                        )
                    ) as "availability"
                FROM offers o
                JOIN retail_products rp ON rp.assortment_unit_id = o.assortment_unit_id
                WHERE o.kladr_id = :kladrId
                AND rp.retail_product_id = ANY(:retailProductIds)
                GROUP BY o.assortment_unit_id
            SQL;

        return $this->connection->executeQuery(
            $sql,
            [
                'kladrId'          => $kladrId,
                'retailProductIds' => DataStructure::transformPHPArrayToPostgresTextArray($retailProductIds),
            ]
        )->iterateAssociative();
    }

    public function isEndCategory(int $categoryId, string $kladrId): bool
    {
        $sql = 'SELECT 1 FROM offers o WHERE o.category_ids[array_upper(o.category_ids, 1)] = :categoryId AND o.kladr_id = :kladrId LIMIT 1';

        $result = $this->connection->executeQuery(
            $sql,
            [
                'kladrId'    => $kladrId,
                'categoryId' => $categoryId,
            ]
        )->fetchOne();

        return (bool) ($result ?? 0);
    }

    /**
     * @return \Traversable<int, array{'assortmentUnitId': int, 'priceWithoutDiscount': float, 'priceForPreorder': float, 'priceForWaiting': float, 'discountForPreorder': int, 'discountForWaiting': int, 'wholesalePrice': float, 'availability': string}>
     */
    public function fetchByBaseProductIdAndKladrId(int $baseProductId, string $kladrId): \Traversable
    {
        $sql = <<<SQL
                SELECT
                    o.assortment_unit_id                as "assortmentUnitId",
                    MAX(o.price_without_discount)       as "priceWithoutDiscount",
                    MIN(o.price_for_preorder)           as "priceForPreorder",
                    MIN(o.price_for_waiting)            as "priceForWaiting",
                    MIN(o.discount_for_preorder)        as "discountForPreorder",
                    MIN(o.discount_for_waiting)         as "discountForWaiting",
                    MIN(o.wholesale_price)              as "wholesalePrice",
                    jsonb_agg(
                        json_build_object(
                            'tradePointId',              o.tradepoint_id,
                            'quantityInStorage',         o.quantity_in_storage,
                            'quantityInStorageUnpacked', o.quantity_in_storage_unpacked,
                            'quantityFromSuppliers',     o.quantity_from_suppliers
                        )
                    ) as "availability"
                FROM offers o
                JOIN retail_products rp ON rp.assortment_unit_id = o.assortment_unit_id
                WHERE o.kladr_id = :kladrId AND rp.retail_product_id = :baseProductId
                GROUP BY o.assortment_unit_id
            SQL;

        return $this->connection->executeQuery(
            $sql,
            [
                'kladrId'       => $kladrId,
                'baseProductId' => $baseProductId,
            ]
        )->iterateAssociative();
    }

    /**
     * @param array<int> $categoryIds
     *
     * @return \Traversable<int, array{'assortmentUnitId': int, 'priceWithoutDiscount': float, 'priceForPreorder': float, 'priceForWaiting': float, 'discountForPreorder': int, 'discountForWaiting': int, 'wholesalePrice': float, 'availability': string}>
     */
    public function fetchByCategoryIdsAndKladrIdGroupedByCategoryId(array $categoryIds, string $kladrId, int $limitInEachCategory): \Traversable
    {
        $sql = <<<SQL
                SELECT
                    o.assortment_unit_id          as "assortmentUnitId",
                    MAX(o.price_without_discount) as "priceWithoutDiscount",
                    MIN(o.price_for_preorder)     as "priceForPreorder",
                    MIN(o.price_for_waiting)      as "priceForWaiting",
                    MIN(o.discount_for_preorder)  as "discountForPreorder",
                    MIN(o.discount_for_waiting)   as "discountForWaiting",
                    MIN(o.wholesale_price)        as "wholesalePrice",
                    jsonb_agg(
                        json_build_object(
                            'tradePointId',              o.tradepoint_id,
                            'quantityInStorage',         o.quantity_in_storage,
                            'quantityInStorageUnpacked', o.quantity_in_storage_unpacked,
                            'quantityFromSuppliers',     o.quantity_from_suppliers
                        )
                    ) as "availability"
                FROM offers o
                WHERE o.kladr_id = :kladrId
                AND o.assortment_unit_id IN (
                    SELECT subq.assortment_unit_id
                    FROM (
                      SELECT assortment_unit_id, "categoryId", row_number() OVER (PARTITION BY "categoryId") AS assortment_unit_id_num
                      FROM (
                          SELECT unnest(category_ids) as "categoryId", assortment_unit_id, kladr_id
                          FROM offers
                      ) as assortment_unit_id_with_category_id
                      WHERE assortment_unit_id_with_category_id.kladr_id = :kladrId
                      AND assortment_unit_id_with_category_id."categoryId" = ANY (:categoryIds)
                      GROUP BY assortment_unit_id_with_category_id.assortment_unit_id, assortment_unit_id_with_category_id."categoryId"
                    ) subq
                    WHERE assortment_unit_id_num <= :limitInEachCategory
                )
                GROUP BY o.assortment_unit_id, o.category_ids
            SQL;

        return $this->connection->executeQuery(
            $sql,
            [
                'kladrId'             => $kladrId,
                'categoryIds'         => DataStructure::transformPHPArrayToPostgresTextArray($categoryIds),
                'limitInEachCategory' => $limitInEachCategory,
            ]
        )->iterateAssociative();
    }

    /**
     * @param list<int> $categoryIds
     *
     * @return array<int, int>
     */
    public function fetchTotalCountByCategoryIds(array $categoryIds, string $kladrId): array
    {
        $sql = <<<SQL
                SELECT subq."categoryId" as "categoryId", count(DISTINCT subq.assortment_unit_id) as "totalCount"
                FROM (
                    SELECT unnest(category_ids) as "categoryId", assortment_unit_id
                    FROM offers o
                    WHERE o.kladr_id = :kladrId
                ) subq
                WHERE subq."categoryId" = ANY (:categoryIds)
                GROUP BY subq."categoryId"
            SQL;

        return $this->connection->executeQuery(
            $sql,
            [
                'kladrId'     => $kladrId,
                'categoryIds' => DataStructure::transformPHPArrayToPostgresTextArray($categoryIds),
            ]
        )->fetchAllKeyValue();
    }
}
