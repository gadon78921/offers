<?php

declare(strict_types=1);

namespace App\Esb\In\KaisCountry;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @codeCoverageIgnore
 */
final class KaisCountryHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function handle(KaisCountryMessage $entity): void
    {
        $this->entityManager->persist($entity);

        if ($entity->isDeleted()) {
            $this->entityManager->remove($entity);
        }

        $this->entityManager->flush();
    }
}
