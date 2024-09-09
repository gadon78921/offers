<?php

declare(strict_types=1);

namespace App\Esb\In\RetailProduct;

use App\OffersBuilding\Commands\BuildOffersByAssortmentUnitIds;
use App\OffersBuilding\Commands\RemoveOffersByAssortmentUnitIds;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @codeCoverageIgnore
 */
final class RetailProductHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageBusInterface $messageBus
    ) {}

    public function handle(RetailProductMessage $entity): void
    {
        $this->entityManager->persist($entity);

        if ($entity->isDeleted() || 1 === $entity->getDontSell()) {
            $this->entityManager->remove($entity);
            $this->messageBus->dispatch(new RemoveOffersByAssortmentUnitIds([$entity->getAssortmentUnitId()]));
        }

        $this->entityManager->flush();
        $this->messageBus->dispatch(new BuildOffersByAssortmentUnitIds([$entity->getAssortmentUnitId()]));
    }
}
