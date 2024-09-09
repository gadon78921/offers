<?php

declare(strict_types=1);

namespace App\OffersApi\Repository\KaisProducts;

use Doctrine\DBAL\Connection;
use MartinGeorgiev\Utils\DataStructure;

final class KaisProductsDatabaseAccessObject
{
    public function __construct(
        private readonly Connection $connection,
    ) {}

    /**
     * @param array<int> $kaisProductIds
     *
     * @return array<int>
     */
    public function fetchSubstitutesAssortmentUnitIds(array $kaisProductIds): array
    {
        $sql = <<<SQL
                SELECT DISTINCT rp.assortment_unit_id
                FROM (
                    SELECT unnest(substitute_kais_product_ids) as "substituteKaisProductId"
                    FROM kais_products
                    WHERE kais_product_id = ANY (:kaisProductIds)
                    ORDER BY array_position(:kaisProductIds, kais_product_id)
                ) as subq
                JOIN retail_products rp ON rp.kais_product_id = subq."substituteKaisProductId"
                WHERE rp.assortment_unit_id NOT IN (
                    SELECT rp.assortment_unit_id
                    FROM retail_products rp
                    WHERE rp.kais_product_id = ANY (:kaisProductIds)
                    GROUP BY rp.assortment_unit_id
                )
            SQL;

        return $this->connection->executeQuery(
            $sql,
            [
                'kaisProductIds' => DataStructure::transformPHPArrayToPostgresTextArray($kaisProductIds),
            ]
        )->fetchFirstColumn();
    }

    /**
     * @return array<int>
     */
    public function fetchAnalogsAssortmentUnitIdsByTransliteratedTradeName(string $transliteratedTradeName): array
    {
        $sql = <<<SQL
                SELECT DISTINCT rp.assortment_unit_id
                FROM kais_products kp
                JOIN kais_products kp2 ON kp2.generic_id = kp.generic_id
                JOIN retail_products rp ON rp.kais_product_id = kp.kais_product_id
                WHERE kp2.transliterated_full_trade_name = :transliteratedTradeName
            SQL;

        return $this->connection->executeQuery($sql, ['transliteratedTradeName' => $transliteratedTradeName])->fetchFirstColumn();
    }

    public function fetchFullTradeNameByTransliteratedTradeName(string $transliteratedTradeName): string
    {
        $sql = 'SELECT full_trade_name FROM kais_products WHERE transliterated_full_trade_name = :transliteratedTradeName LIMIT 1';

        return $this->connection->executeQuery($sql, ['transliteratedTradeName' => $transliteratedTradeName])->fetchOne();
    }

    /**
     * @return array<int>
     */
    public function fetchAssortmentUnitIdsByTradeName(string $tradeName): array
    {
        $sql = <<<SQL
                SELECT DISTINCT rp.assortment_unit_id
                FROM kais_products kp
                JOIN retail_products rp ON rp.kais_product_id = kp.kais_product_id
                WHERE full_trade_name ILIKE :tradeName
            SQL;

        return $this->connection->executeQuery($sql, ['tradeName' => $tradeName . '%'])->fetchFirstColumn();
    }

    /**
     * @return array{'productName': string, 'indicatorForUse': string, 'contraindicationsForUse': string, 'sideEffect': string}
     */
    public function fetchAssortmentUnitDescriptionByTradeName(string $tradeName): array
    {
        $sql = <<<SQL
                SELECT product_name              as "productName",
                       indications_for_use       as "indicatorForUse",
                       contraindications_for_use as "contraindicationsForUse",
                       side_effect               as "sideEffect"
                FROM assortment_unit_specifications
                WHERE product_name ILIKE :tradeName
                LIMIT 1
            SQL;

        return $this->connection->executeQuery($sql, ['tradeName' => $tradeName . '%'])->fetchAssociative();
    }
}
