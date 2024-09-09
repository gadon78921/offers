<?php

declare(strict_types=1);

namespace App\Esb\In\KaisGeneric;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @codeCoverageIgnore
 */
final class KaisGenericHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function handle(KaisGenericMessage $entity): void
    {
        $this->entityManager->persist($entity);

        if ($entity->isDeleted()) {
            $this->entityManager->remove($entity);
        }

        $this->entityManager->flush();
    }
}
