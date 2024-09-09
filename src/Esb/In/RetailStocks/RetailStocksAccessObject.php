<?php

declare(strict_types=1);

namespace App\Esb\In\RetailStocks;

use App\Esb\In\RetailStocks\Dto\RetailStocksCollection;
use App\Esb\In\RetailStocks\Dto\RetailStocksDTO;
use Doctrine\DBAL\Connection;

/**
 * @codeCoverageIgnore
 */
final class RetailStocksAccessObject
{
    private const RETAIL_STOCKS_TABLE = 'retail_stocks';

    public function __construct(
        private readonly Connection $connection
    ) {}

    public function saveBulk(RetailStocksCollection $retailStocksCollection): void
    {
        if ($retailStocksCollection->isEmpty()) {
            return;
        }

        $sql = '
            INSERT INTO ' . self::RETAIL_STOCKS_TABLE . ' (
                kais_product_id, firm_subdivision_id, store_id, free_qty, divided_free_qty, retail_price_with_tax, avg_income_price_with_tax
            )
            VALUES ' . $this->prepareValuesToInsert($retailStocksCollection) . '
            ON CONFLICT (kais_product_id, firm_subdivision_id) DO UPDATE SET
                store_id = EXCLUDED.store_id,
                free_qty = EXCLUDED.free_qty,
                divided_free_qty = EXCLUDED.divided_free_qty,
                retail_price_with_tax = EXCLUDED.retail_price_with_tax,
                avg_income_price_with_tax = EXCLUDED.avg_income_price_with_tax,
                updated_at = NOW()
         ';

        $this->connection->executeStatement($sql);
    }

    private function prepareValuesToInsert(RetailStocksCollection $retailStocksCollection): string
    {
        $valuesCollection = $retailStocksCollection->map(static function (?RetailStocksDTO $stockDto = null) {
            $stock = null === $stockDto ? [] : $stockDto->toArray();

            return '(' . implode(',', $stock) . ')';
        });

        return implode(',', $valuesCollection->toArray());
    }
}
