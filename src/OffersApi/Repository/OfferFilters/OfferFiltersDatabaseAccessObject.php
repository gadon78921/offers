<?php

declare(strict_types=1);

namespace App\OffersApi\Repository\OfferFilters;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use MartinGeorgiev\Utils\DataStructure;

final class OfferFiltersDatabaseAccessObject
{
    public function __construct(
        private readonly Connection $connection,
    ) {}

    /**
     * @param array<int> $assortmentUnitIds
     *
     * @return array<int, array{'type': string, 'itemType': string, 'title': string, 'viewType': string, 'gender': string, 'isAvailableForFastAccess': bool, 'name': string, 'possibleValues': string}>
     */
    public function fetchPossibleFiltersByAssortmentUnitIdsAndKladrId(string $kladrId, array $assortmentUnitIds): array
    {
        $sql = <<<SQL
                (
                    WITH unpacked_filter_properties as (
                        SELECT (jsonb_each_text(properties)).* FROM filters WHERE assortment_unit_id IN (
                            SELECT DISTINCT o.assortment_unit_id
                            FROM offers o
                            WHERE o.assortment_unit_id IN (:assortmentUnitIds)
                            AND o.kladr_id = :kladrId
                        )
                    ), counter_filter_values as (
                        SELECT key as "filterName", value as "filterValue", count(*) as "count" FROM unpacked_filter_properties GROUP BY value, key ORDER BY value ASC
                    )
                    SELECT
                        MIN(meta.type)                             as "type",
                        MIN(meta.item_type)                        as "itemType",
                        MIN(meta.title)                            as "title",
                        MIN(meta.view_type)                        as "viewType",
                        MIN(meta.gender)                           as "gender",
                        bool_or(meta.is_available_for_fast_access) as "isAvailableForFastAccess",
                        "filterName"                               as "name",
                        jsonb_agg(
                            jsonb_build_object(
                                'key',   "filterValue",
                                'value', "filterValue",
                                'count', count
                           )
                        ) as "possibleValues"
                    FROM counter_filter_values cfv
                    CROSS JOIN LATERAL (SELECT type, item_type, title, view_type, gender, is_available_for_fast_access FROM filters_meta_info meta WHERE meta.filter_name = cfv."filterName" LIMIT 1) as meta
                    GROUP BY "filterName"
                    ORDER BY "filterName"
                )
                UNION ALL
                (
                    WITH counter_for_in_trade_points AS (
                        SELECT o.tradepoint_id as "tradepoint", count(o.assortment_unit_id) as "count"
                        FROM offers o
                        WHERE o.assortment_unit_id IN (:assortmentUnitIds)
                        AND o.kladr_id = :kladrId
                        GROUP BY o.tradepoint_id
                    )
                    SELECT
                        MIN(meta.type)                             as "type",
                        MIN(meta.item_type)                        as "itemType",
                        MIN(meta.title)                            as "title",
                        MIN(meta.view_type)                        as "viewType",
                        MIN(meta.gender)                           as "gender",
                        bool_or(meta.is_available_for_fast_access) as "isAvailableForFastAccess",
                        MIN(meta."filterName")                     as "filterName",
                        jsonb_agg(
                            jsonb_build_object(
                                'key',   tradepoint,
                                'value', tradepoint,
                                'count', count
                            )
                        )
                    FROM counter_for_in_trade_points
                    CROSS JOIN LATERAL (SELECT type, item_type, title, view_type, gender, is_available_for_fast_access, filter_name as "filterName" FROM filters_meta_info meta WHERE meta.filter_name = 'inTradePoints') as meta
                    GROUP BY meta."filterName"
                )
            SQL;

        return $this->connection->executeQuery(
            $sql,
            [
                'kladrId'           => $kladrId,
                'assortmentUnitIds' => $assortmentUnitIds,
            ],
            [
                'assortmentUnitIds' => ArrayParameterType::INTEGER,
            ]
        )->fetchAllAssociative();
    }

    /**
     * @return array<int, array{'type': string, 'itemType': string, 'title': string, 'viewType': string, 'gender': string, 'isAvailableForFastAccess': bool, 'name': string, 'possibleValues': string}>
     */
    public function fetchPossibleFiltersByCategoryIdAndKladrId(string $kladrId, int $categoryId): array
    {
        $sql = <<<SQL
                (
                    WITH unpacked_filter_properties as (
                        SELECT (jsonb_each_text(properties)).* FROM filters WHERE assortment_unit_id IN (
                            SELECT DISTINCT o.assortment_unit_id
                            FROM offers o
                            WHERE :categoryId = ANY(o.category_ids)
                            AND o.kladr_id = :kladrId
                        )
                    ), counter_filter_values as (
                        SELECT key as "filterName", value as "filterValue", count(*) as "count" FROM unpacked_filter_properties GROUP BY value, key ORDER BY value ASC
                    )
                    SELECT
                        MIN(meta.type)                             as "type",
                        MIN(meta.item_type)                        as "itemType",
                        MIN(meta.title)                            as "title",
                        MIN(meta.view_type)                        as "viewType",
                        MIN(meta.gender)                           as "gender",
                        bool_or(meta.is_available_for_fast_access) as "isAvailableForFastAccess",
                        "filterName"                               as "name",
                        jsonb_agg(
                            jsonb_build_object(
                                'key',   "filterValue",
                                'value', "filterValue",
                                'count', count
                           )
                        ) as "possibleValues"
                    FROM counter_filter_values cfv
                    CROSS JOIN LATERAL (SELECT type, item_type, title, view_type, gender, is_available_for_fast_access FROM filters_meta_info meta WHERE meta.filter_name = cfv."filterName" LIMIT 1) as meta
                    GROUP BY "filterName"
                    ORDER BY "filterName"
                )
                UNION ALL
                (
                    WITH counter_for_in_trade_points AS (
                        SELECT o.tradepoint_id as "tradepoint", count(o.assortment_unit_id) as "count"
                        FROM offers o
                        WHERE :categoryId = ANY(o.category_ids)
                        AND o.kladr_id = :kladrId
                        GROUP BY o.tradepoint_id
                    )
                    SELECT
                        MIN(meta.type)                             as "type",
                        MIN(meta.item_type)                        as "itemType",
                        MIN(meta.title)                            as "title",
                        MIN(meta.view_type)                        as "viewType",
                        MIN(meta.gender)                           as "gender",
                        bool_or(meta.is_available_for_fast_access) as "isAvailableForFastAccess",
                        MIN(meta."filterName")                     as "filterName",
                        jsonb_agg(
                            jsonb_build_object(
                                'key',   tradepoint,
                                'value', tradepoint,
                                'count', count
                            )
                        )
                    FROM counter_for_in_trade_points
                    CROSS JOIN LATERAL (SELECT type, item_type, title, view_type, gender, is_available_for_fast_access, filter_name as "filterName" FROM filters_meta_info meta WHERE meta.filter_name = 'inTradePoints') as meta
                    GROUP BY meta."filterName"
                )
            SQL;

        return $this->connection->executeQuery(
            $sql,
            [
                'kladrId'    => $kladrId,
                'categoryId' => $categoryId,
            ]
        )->fetchAllAssociative();
    }

    /**
     * @param array<int>                            $assortmentUnitIds
     * @param array<string, array<int, int|string>> $filters
     *
     * @return array<int>
     */
    public function filterAssortmentUnitIds(array $assortmentUnitIds, string $kladrId, array $filters): array
    {
        $tradePointIds = $filters['inTradePoints'] ?? [];
        unset($filters['inTradePoints']);

        $subSql = 'SELECT assortment_unit_id FROM offers WHERE kladr_id = :kladrId AND assortment_unit_id = ANY(:assortmentUnitIds)';
        $subSql .= empty($tradePointIds) ? '' : ' AND tradepoint_id = ANY(:tradePointIds)';

        $sql = <<<SQL
                SELECT assortment_unit_id as "assortmentUnitId"
                FROM filters
                WHERE assortment_unit_id IN ($subSql)
            SQL;

        foreach ($filters as $filterName => $filterValues) {
            $orCondition = [];
            foreach ($filterValues as $filterValue) {
                $orCondition[] = 'properties @> \'{"' . $filterName . '":"' . $filterValue . '"}\'';
            }

            $andCondition[] = empty($orCondition) ? '' : '(' . implode(' OR ', $orCondition) . ')';
        }

        $sql .= empty($andCondition) ? '' : ' AND ' . implode(' AND ', $andCondition);
        $sql .= ' ORDER BY array_position(:assortmentUnitIds, assortment_unit_id)';

        return $this->connection->executeQuery(
            $sql,
            [
                'kladrId'           => $kladrId,
                'assortmentUnitIds' => DataStructure::transformPHPArrayToPostgresTextArray($assortmentUnitIds),
                'tradePointIds'     => DataStructure::transformPHPArrayToPostgresTextArray($tradePointIds),
            ]
        )->fetchFirstColumn();
    }

    /**
     * @param array<string, array<int, int|string>> $filters
     *
     * @return array<int>
     */
    public function filterByCategoryId(int $categoryId, string $kladrId, array $filters): array
    {
        $tradePointIds = $filters['inTradePoints'] ?? [];
        unset($filters['inTradePoints']);

        $subSql = 'SELECT assortment_unit_id FROM offers WHERE kladr_id = :kladrId AND :categoryId = ANY(category_ids)';
        $subSql .= empty($tradePointIds) ? '' : ' AND tradepoint_id = ANY(:tradePointIds)';

        $sql = <<<SQL
                SELECT assortment_unit_id as "assortmentUnitId"
                FROM filters
                WHERE assortment_unit_id IN ($subSql)
            SQL;

        foreach ($filters as $filterName => $filterValues) {
            $orCondition = [];
            foreach ($filterValues as $filterValue) {
                $orCondition[] = 'properties @> \'{"' . $filterName . '":"' . $filterValue . '"}\'';
            }

            $andCondition[] = empty($orCondition) ? '' : '(' . implode(' OR ', $orCondition) . ')';
        }

        $sql .= empty($andCondition) ? '' : ' AND ' . implode(' AND ', $andCondition);

        return $this->connection->executeQuery(
            $sql,
            [
                'kladrId'       => $kladrId,
                'categoryId'    => $categoryId,
                'tradePointIds' => DataStructure::transformPHPArrayToPostgresTextArray($tradePointIds),
            ]
        )->fetchFirstColumn();
    }
}
