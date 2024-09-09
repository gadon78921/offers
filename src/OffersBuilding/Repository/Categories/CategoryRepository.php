<?php

declare(strict_types=1);

namespace App\OffersBuilding\Repository\Categories;

use Doctrine\Common\Collections\ArrayCollection;

final class CategoryRepository
{
    /** @var ArrayCollection<int, array<int>> */
    private ArrayCollection $collection;

    public function __construct(
        private readonly CategoryDatabaseAccessObject $dao,
    ) {
        $this->collection = new ArrayCollection();
    }

    /**
     * @param array<int> $assortmentUnitIds
     */
    public function fill(array $assortmentUnitIds): void
    {
        $this->collection = $this->dao->fetchAssortmentUnitIdsWithCategories($assortmentUnitIds);
    }

    /** @return array<int> */
    public function getByAssortmentUnitId(int $assortmentUnitId): array
    {
        $categoryIds = $this->collection->get($assortmentUnitId);

        return $categoryIds ?? [];
    }
}
