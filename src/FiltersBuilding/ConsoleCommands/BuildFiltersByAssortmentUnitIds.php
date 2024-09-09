<?php

declare(strict_types=1);

namespace App\FiltersBuilding\ConsoleCommands;

use App\FiltersBuilding\Service\BuildFiltersService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class BuildFiltersByAssortmentUnitIds extends Command
{
    protected static $defaultName = 'build:filters:by-assortment-unit-ids';

    public function __construct(
        private readonly BuildFiltersService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName(self::$defaultName)
            ->setDescription('Перестроить фильтры по id АП')
            ->addArgument('assortmentUnitIds', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Id АП, для которых будет перестроена витрина')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $assortmentUnitIds = $input->getArgument('assortmentUnitIds');
        $this->service->build($assortmentUnitIds);

        return 0;
    }
}
