<?php

declare(strict_types=1);

namespace App\Esb\In\RetailSupplier;

use App\Esb\In\RetailTradePoint\RetailTradePointRepository;
use App\OffersBuilding\Commands\BuildOffersByTradePointIds;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class RetailSupplierHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RetailTradePointRepository $tradePointRepository,
        private readonly MessageBusInterface $messageBus,
    ) {}

    public function handle(RetailSupplierMessage $message): void
    {
        $this->entityManager->persist($message);

        if ($message->isDeleted()) {
            $this->entityManager->remove($message);
        }

        $this->entityManager->flush();

        $tradePointIdsByKladrId = $this->tradePointRepository->getTradePointIdsBySupplierId($message->getSupplierId());
        foreach ($tradePointIdsByKladrId as $tradePointIds) {
            $this->messageBus->dispatch(new BuildOffersByTradePointIds($tradePointIds));
        }
    }
}
