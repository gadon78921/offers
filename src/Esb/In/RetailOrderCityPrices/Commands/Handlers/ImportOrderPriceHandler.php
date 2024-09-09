<?php

declare(strict_types=1);

namespace App\Esb\In\RetailOrderCityPrices\Commands\Handlers;

use App\Esb\In\RetailOrderCityPrices\Commands\ImportOrderPrice;
use Doctrine\DBAL\Connection;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Process\Process;

/**
 * @codeCoverageIgnore
 */
final class ImportOrderPriceHandler implements MessageHandlerInterface
{
    private const RETAIL_PRICES_FROM_CITY = 'retail_prices_from_city';
    private const RETAIL_PRICES           = 'retail_prices';

    public function __construct(
        private readonly Connection $connection,
        private readonly string $dsn,
    ) {}

    public function __invoke(ImportOrderPrice $command): void
    {
        $kladrId = $command->kladrId;
        $table   = self::RETAIL_PRICES_FROM_CITY . '_' . $kladrId;

        $this->connection->executeStatement('DROP TABLE IF EXISTS ' . $table);
        $this->connection->executeStatement('CREATE TABLE ' . $table . ' (LIKE ' . self::RETAIL_PRICES_FROM_CITY . ')');

        $process = new Process(['psql', $this->dsn, '-c', '\copy ' . $table . " FROM '" . $command->filePath . "' DELIMITER ';' CSV"]);
        $process->mustRun();

        $this->connection->beginTransaction();
        try {
            $this->connection->executeStatement(
                'DELETE FROM ' . self::RETAIL_PRICES . ' WHERE kladr_id = :kladrId',
                ['kladrId' => $kladrId]
            );

            $this->connection->executeStatement(
                'INSERT INTO ' . self::RETAIL_PRICES .
                '(assortment_unit_id, price, price_for_preorder, discount_for_preorder, price_for_waiting, discount_for_waiting, is_fixed_discount, kladr_id)
                 SELECT assortment_unit_id, price, price_for_preorder, discount_for_preorder, price_for_waiting, discount_for_waiting, is_fixed_discount,' . $kladrId .
                ' FROM ' . $table
            );
            $this->connection->commit();
        } catch (\Throwable $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }
}
