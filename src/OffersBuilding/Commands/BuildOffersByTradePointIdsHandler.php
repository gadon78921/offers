<?php

declare(strict_types=1);

namespace App\OffersBuilding\Commands;

use App\OffersBuilding\Services\BuildOffersByTradePointService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class BuildOffersByTradePointIdsHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly BuildOffersByTradePointService $service
    ) {}

    public function __invoke(BuildOffersByTradePointIds $command): void
    {
        $this->service->build($command->tradePointIds);
    }
}
