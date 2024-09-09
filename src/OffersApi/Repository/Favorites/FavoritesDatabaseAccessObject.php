<?php

declare(strict_types=1);

namespace App\OffersApi\Repository\Favorites;

use Doctrine\DBAL\Connection;

final class FavoritesDatabaseAccessObject
{
    public function __construct(
        private readonly Connection $connection,
    ) {}

    /**
     * @return list<int>
     */
    public function fetchByClientId(int $clientId): array
    {
        $sql = <<<SQL
                SELECT DISTINCT auu.assortment_unit_id
                FROM client_favorites cf
                JOIN assortment_unit_specifications auu ON auu.base_product_id = cf.base_product_id
                WHERE cf.client_id = :clientId
            SQL;

        return $this->connection->executeQuery($sql, ['clientId' => $clientId])->fetchFirstColumn();
    }

    public function add(int $clientId, int $assortmentUnitId): void
    {
        $sql = <<<SQL
                INSERT INTO client_favorites (client_id, base_product_id)
                SELECT :clientId, base_product_id
                FROM assortment_unit_specifications
                WHERE assortment_unit_id = :assortmentUnitId
                ON CONFLICT DO NOTHING
            SQL;

        $this->connection->executeStatement($sql, ['clientId' => $clientId, 'assortmentUnitId' => $assortmentUnitId]);
    }

    public function remove(int $clientId, int $assortmentUnitId): void
    {
        $sql = <<<SQL
                DELETE FROM client_favorites cf
                USING assortment_unit_specifications auu
                WHERE cf.base_product_id = auu.base_product_id
                AND cf.client_id = :clientId
                AND auu.assortment_unit_id = :assortmentUnitId
            SQL;

        $this->connection->executeStatement($sql, ['clientId' => $clientId, 'assortmentUnitId' => $assortmentUnitId]);
    }
}
