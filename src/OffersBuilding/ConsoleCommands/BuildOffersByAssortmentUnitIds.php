<?php

declare(strict_types=1);

namespace App\OffersBuilding\ConsoleCommands;

use App\OffersBuilding\Services\BuildOffersByKladrIdService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class BuildOffersByAssortmentUnitIds extends Command
{
    protected static $defaultName = 'build:offers:by-assortment-unit-ids';

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
            ->setDescription('Перестроить витрину по id ассортиментных позиций')
            ->addArgument('assortmentUnitIds', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Ассортиментные позиции')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $assortmentUnitIds = $input->getArgument('assortmentUnitIds');
        $this->command->build(assortmentUnitIds: $assortmentUnitIds);

        return 0;
    }
}
