<?php

declare(strict_types=1);

namespace App\Esb\In\RetailAssortmentUnit;

use App\OffersBuilding\Commands\BuildOffersByAssortmentUnitIds;
use App\OffersBuilding\Commands\RemoveOffersByAssortmentUnitIds;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @codeCoverageIgnore
 */
final class RetailAssortmentUnitHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageBusInterface $messageBus
    ) {}

    public function handle(RetailAssortmentUnitMessage $entity): void
    {
        $this->entityManager->persist($entity);

        if ($entity->isDeleted()) {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
            $this->messageBus->dispatch(new RemoveOffersByAssortmentUnitIds([$entity->getAssortmentUnitId()]));

            return;
        }

        $this->entityManager->flush();
        $this->messageBus->dispatch(new BuildOffersByAssortmentUnitIds([$entity->getAssortmentUnitId()]));
    }
}
