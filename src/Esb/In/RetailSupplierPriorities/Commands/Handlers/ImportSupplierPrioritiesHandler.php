<?php

declare(strict_types=1);

namespace App\Esb\In\RetailSupplierPriorities\Commands\Handlers;

use App\Esb\In\RetailSupplierPriorities\Commands\ImportSupplierPriorities;
use Doctrine\DBAL\Connection;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Process\Process;

/**
 * @codeCoverageIgnore
 */
final class ImportSupplierPrioritiesHandler implements MessageHandlerInterface
{
    private const RETAIL_SUPPLIER_PRIORITIES_REGION = 'retail_supplier_priorities_region';
    private const RETAIL_SUPPLIER_PRIORITIES        = 'retail_supplier_priorities';

    public function __construct(
        private readonly Connection $connection,
        private readonly string $dsn,
    ) {}

    public function __invoke(ImportSupplierPriorities $command): void
    {
        $regionId = $command->regionId;
        $table    = self::RETAIL_SUPPLIER_PRIORITIES_REGION . '_' . $regionId;

        $this->connection->executeStatement('DROP TABLE IF EXISTS ' . $table);
        $this->connection->executeStatement('CREATE TABLE ' . $table . ' (LIKE ' . self::RETAIL_SUPPLIER_PRIORITIES_REGION . ')');

        $process = new Process(['psql', $this->dsn, '-c', '\copy ' . $table . " FROM '" . $command->filePath . "' DELIMITER ';' CSV HEADER"]);
        $process->mustRun();

        $this->connection->beginTransaction();
        try {
            $this->connection->executeStatement(
                'DELETE FROM ' . self::RETAIL_SUPPLIER_PRIORITIES . ' WHERE region_id = :regionId',
                ['regionId' => $regionId]
            );

            $this->connection->executeStatement(
                'INSERT INTO ' . self::RETAIL_SUPPLIER_PRIORITIES .
                '(retail_product_id, trade_point_id, supplier_list_ids, region_id)
                 SELECT retail_product_id, trade_point_id, string_to_array(supplier_list_ids, \',\'), ' . $regionId .
                ' FROM ' . $table
            );
            $this->connection->commit();
        } catch (\Throwable $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }
}
