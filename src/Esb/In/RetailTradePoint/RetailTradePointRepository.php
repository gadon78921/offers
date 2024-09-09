<?php

declare(strict_types=1);

namespace App\Esb\In\RetailTradePoint;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RetailTradePointMessage>
 */
final class RetailTradePointRepository extends ServiceEntityRepository
{
    private RetailTradePointAccessObject $dao;

    public function __construct(ManagerRegistry $registry, RetailTradePointAccessObject $dao)
    {
        $this->dao = $dao;
        parent::__construct($registry, RetailTradePointMessage::class);
    }

    /** @return array{'kladr_id': string, array{int}} */
    public function getTradePointIdsBySupplierId(int $supplierId): array
    {
        return $this->dao->getTradePointIdsBySupplierId((string) $supplierId);
    }

    /** @return array{int} */
    public function getTradePointIdsByFirmId(string $firmId): array
    {
        return $this->dao->getTradePointIdsByFirmId($firmId);
    }
}
