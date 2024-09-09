<?php

declare(strict_types=1);

namespace App\OffersBuilding\ConsoleCommands;

use App\OffersBuilding\Services\BuildOffersByTradePointService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class BuildOffersByTradePoints extends Command
{
    protected static $defaultName = 'build:offers:by-tradepoint-ids';

    public function __construct(
        private BuildOffersByTradePointService $command,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName(self::$defaultName)
            ->setDescription('Перестроить витрину по id торговых точек')
            ->addArgument('tradePointIds', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Id торговых точек, для которых будет перестроена витрина')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tradePointIds = $input->getArgument('tradePointIds');
        $this->command->build($tradePointIds);

        return 0;
    }
}
