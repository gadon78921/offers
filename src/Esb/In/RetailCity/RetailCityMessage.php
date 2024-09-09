<?php

declare(strict_types=1);

namespace App\Esb\In\RetailCity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Monastirevrf\EsbBundle\Messages\EsbMessageInterface;
use Monastirevrf\EsbBundle\Messages\EsbMessageTrait;

#[ORM\Table(name: 'retail_cities')]
#[ORM\Entity]
class RetailCityMessage implements EsbMessageInterface
{
    use EsbMessageTrait;

    #[ORM\Id]
    #[ORM\Column]
    #[JMS\SerializedName('id')]
    private int $retailCityId;

    #[ORM\Column]
    private string $kladrId;

    private bool $deleted;

    public function getRetailCityId(): int
    {
        return $this->retailCityId;
    }

    public function setRetailCityId(int $retailCityId): void
    {
        $this->retailCityId = $retailCityId;
    }

    public function getKladrId(): string
    {
        return $this->kladrId;
    }

    public function setKladrId(string $kladrId): void
    {
        $this->kladrId = $kladrId;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }
}
