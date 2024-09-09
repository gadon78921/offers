<?php

declare(strict_types=1);

namespace App\OffersBuilding\ConsoleCommands;

use App\OffersBuilding\Services\BuildOffersByKladrIdService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class BuildOffersByKaisProductIds extends Command
{
    protected static $defaultName = 'build:offers:by-kais-product-ids';

    public function __construct(
        private BuildOffersByKladrIdService $command,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName(self::$defaultName)
            ->setDescription('Перестроить витрину по id товаров КАИС')
            ->addArgument('kaisProductIds', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Id товаров КАИС')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $kaisProductIds = $input->getArgument('kaisProductIds');
        $this->command->build(kaisProductIds: $kaisProductIds);

        return 0;
    }
}
