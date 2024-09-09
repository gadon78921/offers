<?php

declare(strict_types=1);

namespace App\OffersBuilding\Commands;

use Doctrine\DBAL\Connection;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class DeduplicateBuildOffersByTradePointIdsMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    public function __invoke(BuildOffersByTradePointIds $command): void
    {
        $sql = 'DELETE FROM messenger_messages WHERE body LIKE \'%' . $command->msgHash . '%\'';

        $this->connection->executeStatement($sql);
    }
}
