<?php

declare(strict_types=1);

namespace App\Esb\In\RetailAssortmentUnit;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Monastirevrf\EsbBundle\Messages\EsbMessageInterface;
use Monastirevrf\EsbBundle\Messages\EsbMessageTrait;

#[ORM\Table(name: 'retail_assortment_units')]
#[ORM\Entity]
class RetailAssortmentUnitMessage implements EsbMessageInterface
{
    use EsbMessageTrait;

    #[ORM\Id]
    #[ORM\Column]
    #[JMS\SerializedName('id')]
    private int $assortmentUnitId;

    private bool $deleted;
    private ?int $assortmentCategoryId       = null;
    private ?string $unitedAssortmentUnitIds = null;
    private bool $isNew;

    public function getAssortmentUnitId(): int
    {
        return $this->assortmentUnitId;
    }

    public function setAssortmentUnitId(int $assortmentUnitId): void
    {
        $this->assortmentUnitId = $assortmentUnitId;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    public function getAssortmentCategoryId(): ?int
    {
        return $this->assortmentCategoryId;
    }

    public function setAssortmentCategoryId(?int $assortmentCategoryId): void
    {
        $this->assortmentCategoryId = $assortmentCategoryId;
    }

    public function getUnitedAssortmentUnitIds(): ?string
    {
        return $this->unitedAssortmentUnitIds;
    }

    public function setUnitedAssortmentUnitIds(?string $unitedAssortmentUnitIds): void
    {
        $this->unitedAssortmentUnitIds = $unitedAssortmentUnitIds;
    }

    public function isNew(): bool
    {
        return $this->isNew;
    }

    public function setIsNew(bool $isNew): void
    {
        $this->isNew = $isNew;
    }
}
