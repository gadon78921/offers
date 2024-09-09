<?php

declare(strict_types=1);

namespace App\OffersBuilding\ConsoleCommands;

use App\OffersBuilding\Services\BuildOffersByKladrIdService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class BuildOffersByKladrIds extends Command
{
    protected static $defaultName = 'build:offers:by-kladr-ids';

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
            ->setDescription('Перестроить витрину по id кладров')
            ->addArgument('kladrId', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Кладры городов, для которых будет перестроена витрина')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $kladrIds = $input->getArgument('kladrId');
        $this->command->build($kladrIds);

        return 0;
    }
}
