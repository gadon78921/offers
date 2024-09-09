<?php

declare(strict_types=1);

namespace App\Esb\In\KaisProducer;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @codeCoverageIgnore
 */
final class KaisProducerHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function handle(KaisProducerMessage $entity): void
    {
        $this->entityManager->persist($entity);

        if ($entity->isDeleted()) {
            $this->entityManager->remove($entity);
        }

        $this->entityManager->flush();
    }
}
