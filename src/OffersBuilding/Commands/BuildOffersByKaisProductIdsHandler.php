<?php

declare(strict_types=1);

namespace App\OffersBuilding\Commands;

use App\OffersBuilding\Services\BuildOffersByKladrIdService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class BuildOffersByKaisProductIdsHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly BuildOffersByKladrIdService $service
    ) {}

    public function __invoke(BuildOffersByKaisProductIds $command): void
    {
        $this->service->build(kaisProductIds: $command->kaisProductIds);
    }
}
