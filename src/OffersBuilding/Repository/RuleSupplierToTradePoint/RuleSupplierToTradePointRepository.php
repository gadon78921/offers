<?php

declare(strict_types=1);

namespace App\OffersBuilding\Repository\RuleSupplierToTradePoint;

final class RuleSupplierToTradePointRepository
{
    public function __construct(
        private readonly RuleSupplierToTradePointAccessObject $dao,
    ) {}

    /**
     * @param array{int} $supplierIds
     *
     * @return array<int, array{'supplier_id': int, 'trade_point_id': int|null, 'isForTzOnlyValues': string}>
     */
    public function getRulesBySupplierIds(array $supplierIds, string $kladrId): array
    {
        return $this->dao->getRulesBySupplierIds($supplierIds, $kladrId);
    }
}
