<?php

declare(strict_types=1);

namespace App\FiltersBuilding\Commands;

use App\FiltersBuilding\Service\BuildFiltersService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class BuildFiltersByAssortmentUnitIdHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly BuildFiltersService $service,
    ) {}

    public function __invoke(BuildFiltersByAssortmentUnitId $command): void
    {
        $this->service->build([$command->assortmentUnitId]);
    }
}
