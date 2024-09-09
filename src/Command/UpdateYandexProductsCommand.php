<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'yandex:update-products',
    description: 'Наполнение БД yandex_products',
)]
class UpdateYandexProductsCommand extends Command
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        parent::__construct();
        $this->connection = $connection;
    }

    /**
     * @throws \Throwable
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Update yandex_products table
        $this->connection->transactional(function ($conn) {
            $select = "SELECT kais_product_id
                    FROM kais_products
                    WHERE 
                        (sell_procedure IS NULL OR sell_procedure = 'Без ограничений')
                        AND not_for_yandex_eda = false";

            $conn->executeStatement("
                    DELETE FROM yandex_products 
                    WHERE kais_product_id NOT IN ($select)
                    ");

            $conn->executeStatement("
                    INSERT INTO yandex_products (kais_product_id)
                    $select
                    On CONFLICT(kais_product_id) DO NOTHING;
                ");
        });

        $io->success('yandex_products table has been updated.');

        return Command::SUCCESS;
    }
}
