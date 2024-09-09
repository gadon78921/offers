<?php

declare(strict_types=1);

namespace App\Esb\In\RetailProduct;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Monastirevrf\EsbBundle\Messages\EsbMessageInterface;
use Monastirevrf\EsbBundle\Messages\EsbMessageTrait;

#[ORM\Table(name: 'retail_products')]
#[ORM\Entity]
class RetailProductMessage implements EsbMessageInterface
{
    use EsbMessageTrait;

    #[ORM\Id]
    #[ORM\Column]
    #[JMS\SerializedName('id')]
    private int $retailProductId;

    #[ORM\Column]
    #[JMS\SerializedName('productId')]
    private int $kaisProductId;

    #[ORM\Column]
    private int $assortmentUnitId;

    #[ORM\Column]
    #[JMS\SerializedName('code')]
    private int $retailProductCode;

    private bool $deleted;
    private int $dontSell;

    public function getRetailProductId(): int
    {
        return $this->retailProductId;
    }

    public function setRetailProductId(int $retailProductId): void
    {
        $this->retailProductId = $retailProductId;
    }

    public function getRetailProductCode(): int
    {
        return $this->retailProductCode;
    }

    public function setRetailProductCode(int $retailProductCode): void
    {
        $this->retailProductCode = $retailProductCode;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    public function getKaisProductId(): int
    {
        return $this->kaisProductId;
    }

    public function setKaisProductId(int $kaisProductId): void
    {
        $this->kaisProductId = $kaisProductId;
    }

    public function getAssortmentUnitId(): int
    {
        return $this->assortmentUnitId;
    }

    public function setAssortmentUnitId(int $assortmentUnitId): void
    {
        $this->assortmentUnitId = $assortmentUnitId;
    }

    public function getDontSell(): int
    {
        return $this->dontSell;
    }

    public function setDontSell(int $dontSell): void
    {
        $this->dontSell = $dontSell;
    }
}
