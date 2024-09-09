<?php

declare(strict_types=1);

namespace App\OffersBuilding\Gateway;

final class OfferDatabaseAccessObject extends DatabaseAccessObject
{
    /** @param array<string> $rows */
    public function insert(array $rows): void
    {
        foreach (array_chunk($rows, self::ROWS_NUMBER_FOR_QUERY) as $chunk) {
            $sql = '
            INSERT INTO ' . $this->tableName . ' (kais_product_id, assortment_unit_id, name, tradepoint_id, kladr_id, price_without_discount, price_for_preorder, price_for_waiting, discount_for_preorder, discount_for_waiting, quantity_in_storage, quantity_in_storage_unpacked, quantity_from_suppliers, supplier_ids, wholesale_price, category_ids)
            VALUES ' . implode(',', $chunk) . '
            ON CONFLICT (assortment_unit_id, tradepoint_id)
            DO UPDATE SET
                kladr_id                     = excluded.kladr_id,
                price_without_discount       = excluded.price_without_discount,
                price_for_preorder           = excluded.price_for_preorder,
                price_for_waiting            = excluded.price_for_waiting,
                discount_for_preorder        = excluded.discount_for_preorder,
                discount_for_waiting         = excluded.discount_for_waiting,
                quantity_in_storage          = excluded.quantity_in_storage,
                quantity_in_storage_unpacked = excluded.quantity_in_storage_unpacked,
                quantity_from_suppliers      = excluded.quantity_from_suppliers,
                supplier_ids                 = excluded.supplier_ids,
                wholesale_price              = excluded.wholesale_price,
                name                         = excluded.name,
                category_ids                 = excluded.category_ids
        ';

            try {
                $this->connection->executeStatement($sql);
            } catch (\Throwable $exception) {
                $this->logger->error($exception->getMessage());
                $this->logger->error(json_encode($sql, JSON_THROW_ON_ERROR));
            }
        }
    }
}
