<?php

declare(strict_types=1);

namespace App\OffersBuilding\Commands;

use App\OffersBuilding\Services\RemoveOffersService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RemoveOffersByTradePointIdsHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly RemoveOffersService $service
    ) {}

    public function __invoke(RemoveOffersByTradePointIds $command): void
    {
        $this->service->removeByTradePointIds($command->tradePointIds);
    }
}
