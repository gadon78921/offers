<?php

declare(strict_types=1);

namespace App\OffersBuilding\Repository\Kladr;

use App\OffersBuilding\Repository\TradePoints\TradePointsDatabaseAccessObject;
use Doctrine\Common\Collections\ArrayCollection;

final class KladrRepository
{
    /** @var ArrayCollection<string, string> */
    private ArrayCollection $collection;

    public function __construct(
        private readonly TradePointsDatabaseAccessObject $dao,
    ) {
        $this->collection = new ArrayCollection();
        $this->fill();
    }

    /** @return ArrayCollection<string, string> */
    public function getCollection(): ArrayCollection
    {
        return $this->collection;
    }

    private function fill(): void
    {
        foreach ($this->dao->fetchKladrIds() as $kladr) {
            $this->collection->set($kladr['kladrId'], $kladr['kladrId']);
        }
    }
}
