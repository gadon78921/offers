<?php

declare(strict_types=1);

namespace App\Esb\In\KaisCountry;

use Doctrine\ORM\Mapping as ORM;
use Monastirevrf\EsbBundle\Messages\EsbMessageInterface;
use Monastirevrf\EsbBundle\Messages\EsbMessageTrait;

#[ORM\Table(name: 'kais_countries')]
#[ORM\Entity]
class KaisCountryMessage implements EsbMessageInterface
{
    use EsbMessageTrait;

    #[ORM\Id]
    #[ORM\Column]
    private int $id;

    private bool $deleted;

    #[ORM\Column]
    private string $shortName;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function setShortName(string $shortName): void
    {
        $this->shortName = $shortName;
    }
}
