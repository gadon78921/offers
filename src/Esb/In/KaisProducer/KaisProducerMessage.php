<?php

declare(strict_types=1);

namespace App\Esb\In\KaisProducer;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Monastirevrf\EsbBundle\Messages\EsbMessageInterface;
use Monastirevrf\EsbBundle\Messages\EsbMessageTrait;

#[ORM\Table(name: 'kais_producers')]
#[ORM\Entity]
class KaisProducerMessage implements EsbMessageInterface
{
    use EsbMessageTrait;

    #[ORM\Id]
    #[ORM\Column]
    private int $id;

    private bool $deleted;

    #[ORM\Column]
    private string $name;

    #[ORM\Column]
    private string $transliteratedName;

    #[ORM\Column]
    #[JMS\SerializedName('countryId')]
    private int $kaisCountryId;

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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getTransliteratedName(): string
    {
        return $this->transliteratedName;
    }

    public function setTranslitiratedName(string $transliteratedName): void
    {
        $this->transliteratedName = $transliteratedName;
    }

    public function getKaisCountryId(): int
    {
        return $this->kaisCountryId;
    }

    public function setCountryId(int $kaisCountryId): void
    {
        $this->kaisCountryId = $kaisCountryId;
    }
}
