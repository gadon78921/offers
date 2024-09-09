<?php

declare(strict_types=1);

namespace App\Esb\In\KaisProductPersonCategory;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @codeCoverageIgnore
 */
final class KaisProductPersonCategoryHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function handle(KaisProductPersonCategoryMessage $entity): void
    {
        $this->entityManager->persist($entity);

        if ($entity->isDeleted()) {
            $this->entityManager->remove($entity);
        }

        $this->entityManager->flush();
    }
}
