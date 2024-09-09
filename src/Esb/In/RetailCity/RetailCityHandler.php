<?php

declare(strict_types=1);

namespace App\Esb\In\RetailCity;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @codeCoverageIgnore
 */
final class RetailCityHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function handle(RetailCityMessage $entity): void
    {
        $this->entityManager->persist($entity);

        if ($entity->isDeleted()) {
            $this->entityManager->remove($entity);
        }

        $this->entityManager->flush();
    }
}
