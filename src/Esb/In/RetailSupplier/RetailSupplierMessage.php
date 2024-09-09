<?php

declare(strict_types=1);

namespace App\Esb\In\RetailSupplier;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Monastirevrf\EsbBundle\Messages\EsbMessageInterface;
use Monastirevrf\EsbBundle\Messages\EsbMessageTrait;

#[ORM\Table(name: 'retail_suppliers')]
#[ORM\Entity]
class RetailSupplierMessage implements EsbMessageInterface
{
    use EsbMessageTrait;

    #[ORM\Id]
    #[ORM\Column]
    #[JMS\SerializedName('id')]
    private int $supplierId;

    private bool $deleted;

    #[ORM\Column]
    #[JMS\SerializedName('code')]
    private int $supplierCode;

    public function getSupplierId(): int
    {
        return $this->supplierId;
    }

    public function setSupplierId(int $supplierId): void
    {
        $this->supplierId = $supplierId;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    public function getSupplierCode(): int
    {
        return $this->supplierCode;
    }

    public function setSupplierCode(int $supplierCode): void
    {
        $this->supplierCode = $supplierCode;
    }
}
