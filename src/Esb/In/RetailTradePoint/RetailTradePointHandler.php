<?php

declare(strict_types=1);

namespace App\Esb\In\RetailTradePoint;

use App\Esb\In\RetailCity\RetailCityMessage;
use App\Esb\In\RetailCity\RetailCityRepository;
use App\OffersBuilding\Commands\BuildOffersByTradePointIds;
use App\OffersBuilding\Commands\RemoveOffersByTradePointIds;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @codeCoverageIgnore
 */
final class RetailTradePointHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RetailCityRepository $retailCityRepository,
        private readonly MessageBusInterface $messageBus,
    ) {}

    public function handle(RetailTradePointMessage $entity): void
    {
        /* @var RetailCityMessage $retailCity */
        $retailCity = $this->retailCityRepository->find($entity->getRetailCityId());

        $entity->setKladrId($retailCity?->getKladrId());
        $this->entityManager->persist($entity);

        if ($entity->isDeleted()) {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
            $this->messageBus->dispatch(new RemoveOffersByTradePointIds([$entity->getTradePointId()]));

            return;
        }

        $this->entityManager->flush();
        $this->messageBus->dispatch(new BuildOffersByTradePointIds([$entity->getTradePointId()]));
    }
}
