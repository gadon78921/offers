<?php

declare(strict_types=1);

namespace App\ProductsApi\Repository;

use Doctrine\DBAL\Connection;

final class CatalogSeoAttributesDatabaseAccessObject
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    /**
     * @return array{int: array{'seoTitle': string, 'seoDescription': string}}
     */
    public function fetchAll(): array
    {
        $sql = <<<SQL
                SELECT category_id      as "categoryId",
                       products_title_n as "seoTitle",
                       products_descr_n as "seoDescription"
                FROM category_seo_attributes
            SQL;

        return $this->connection->executeQuery($sql)->fetchAllAssociativeIndexed();
    }
}
