<?php

declare(strict_types=1);

namespace App\Esb\In\RetailSupplierPrices\Commands\Handlers;

use App\Esb\In\RetailSupplierPrices\Commands\ImportSupplierPrice;
use Doctrine\DBAL\Connection;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Process\Process;

/**
 * @codeCoverageIgnore
 */
final class ImportSupplierPriceHandler implements MessageHandlerInterface
{
    private const RETAIL_SUPPLIER_PRICES_FROM_SUPPLIER = 'retail_supplier_prices_from_supplier';
    private const RETAIL_SUPPLIER_PRICES               = 'retail_supplier_prices';

    public function __construct(
        private readonly Connection $connection,
        private readonly string $dsn,
    ) {}

    public function __invoke(ImportSupplierPrice $command): void
    {
        $supplierId = $command->supplierId;
        $table      = self::RETAIL_SUPPLIER_PRICES_FROM_SUPPLIER . '_' . $supplierId;

        $this->connection->executeStatement('DROP TABLE IF EXISTS ' . $table);
        $this->connection->executeStatement('CREATE TABLE ' . $table . ' (LIKE ' . self::RETAIL_SUPPLIER_PRICES_FROM_SUPPLIER . ')');

        $process = new Process(['psql', $this->dsn, '-c', '\copy ' . $table . " FROM '" . $command->filePath . "' DELIMITER ';' CSV HEADER encoding 'windows-1251'"]);
        $process->mustRun();

        $this->connection->beginTransaction();
        try {
            $this->connection->executeStatement(
                'DELETE FROM ' . self::RETAIL_SUPPLIER_PRICES . ' WHERE supplier_id = :supplierId',
                ['supplierId' => $supplierId]
            );

            $this->connection->executeStatement(
                'INSERT INTO ' . self::RETAIL_SUPPLIER_PRICES .
                '(kais_product_id, supplier_price, quantity, cost, supplier_id)
                 SELECT DISTINCT ON (kais_product_id) kais_product_id, supplier_price, quantity, cost,' . $supplierId .
                ' FROM ' . $table .
                ' WHERE is_for_tz_only = false' .
                ' AND is_gnvls_problem = false' .
                ' AND min_qty <= 1' .
                ' AND quantity > 0' .
                ' AND correct_best_before > 0' .
                ' ORDER BY kais_product_id'
            );
            $this->connection->commit();
        } catch (\Throwable $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }
}
