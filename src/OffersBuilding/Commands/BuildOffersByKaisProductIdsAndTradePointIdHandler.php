<?php

declare(strict_types=1);

namespace App\OffersBuilding\Commands;

use App\OffersBuilding\Services\BuildOffersByTradePointService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class BuildOffersByKaisProductIdsAndTradePointIdHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly BuildOffersByTradePointService $service
    ) {}

    public function __invoke(BuildOffersByKaisProductIdsAndTradePointId $command): void
    {
        $tradePointId   = $command->tradePointId;
        $kaisProductIds = $command->kaisProductIds;
        $this->service->build([$tradePointId], kaisProductIds: $kaisProductIds);
    }
}
