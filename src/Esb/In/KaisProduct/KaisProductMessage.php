<?php

declare(strict_types=1);

namespace App\Esb\In\KaisProduct;

use Behat\Transliterator\Transliterator;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Monastirevrf\EsbBundle\Messages\EsbMessageInterface;
use Monastirevrf\EsbBundle\Messages\EsbMessageTrait;

#[ORM\Table(name: 'kais_products')]
#[ORM\Entity]
class KaisProductMessage implements EsbMessageInterface
{
    use EsbMessageTrait;

    #[ORM\Id]
    #[ORM\Column]
    #[JMS\SerializedName('id')]
    private int $kaisProductId;

    #[ORM\Column]
    #[JMS\SerializedName('code')]
    private int $kaisProductCode;

    private bool $deleted;

    private ?string $substituteProductIds = null;

    /** @var array<string> */
    #[ORM\Column(type: 'bigint[]')]
    private array $substituteKaisProductIds;

    #[ORM\Column]
    private ?int $genericId;

    #[ORM\Column]
    private ?string $fullTradeName;

    #[ORM\Column]
    private ?string $transliteratedFullTradeName;

    #[ORM\Column]
    private string $name;

    #[ORM\Column]
    private bool $isGNVLS;

    #[ORM\Column]
    private int $producerId;

    #[ORM\Column]
    private ?string $storageConditions;

    #[ORM\Column]
    private ?string $storageType;

    #[ORM\Column]
    private ?string $sellProcedure;

    #[ORM\Column]
    private ?string $actualSellProcedure;

    #[ORM\Column]
    private ?string $fullAdditionalName;

    #[ORM\Column]
    private ?int $personCategoryId;

    #[ORM\Column]
    private ?int $personCategorySuffixId;

    #[ORM\Column]
    private ?string $fullFormName;

    #[ORM\Column]
    private ?string $fullFirstWrappingName;

    #[ORM\Column]
    private ?int $firstPacking;

    #[ORM\Column]
    private ?int $secondPacking;

    #[ORM\Column]
    private ?string $suffix;

    #[ORM\Column]
    private ?float $weight;

    #[ORM\Column]
    private ?int $length;

    #[ORM\Column]
    private ?int $width;

    #[ORM\Column]
    private ?int $height;

    #[ORM\Column]
    private ?string $indications;

    #[ORM\Column]
    private ?string $contraindications;

    #[ORM\Column]
    private ?string $pharmacodynamics;

    #[ORM\Column]
    private ?string $pharmacokinetics;

    #[ORM\Column]
    private ?string $sideEffects;

    #[ORM\Column]
    private ?string $dosageAndAdministration;

    #[ORM\Column]
    private ?string $ingredients;

    #[ORM\Column]
    private ?string $description;

    #[ORM\Column]
    private ?int $expirationDate;

    #[ORM\Column]
    private ?string $packing;

    #[ORM\Column]
    private ?string $dose;

    #[ORM\Column]
    private ?int $simplifiedDosageFormId;

    #[ORM\Column]
    private bool $notForYandexEda;

    private ?string $GTINs = null;

    /** @var array<string> */
    #[ORM\Column(type: 'text[]')]
    private array $kaisGTINs;

    public function getKaisProductId(): int
    {
        return $this->kaisProductId;
    }

    public function setKaisProductId(int $kaisProductId): void
    {
        $this->kaisProductId = $kaisProductId;
    }

    public function getKaisProductCode(): int
    {
        return $this->kaisProductCode;
    }

    public function setKaisProductCode(int $kaisProductCode): void
    {
        $this->kaisProductCode = $kaisProductCode;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    public function getGenericId(): ?int
    {
        return $this->genericId;
    }

    public function setGenericId(?int $genericId): void
    {
        $this->genericId = $genericId;
    }

    public function getFullTradeName(): ?string
    {
        return $this->fullTradeName;
    }

    public function setFullTradeName(?string $fullTradeName): void
    {
        $this->fullTradeName = $fullTradeName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getIsGNVLS(): bool
    {
        return $this->isGNVLS;
    }

    public function setIsGNVLS(bool $isGNVLS): void
    {
        $this->isGNVLS = $isGNVLS;
    }

    public function getProducerId(): int
    {
        return $this->producerId;
    }

    public function setProducerId(int $producerId): void
    {
        $this->producerId = $producerId;
    }

    public function getStorageConditions(): ?string
    {
        return $this->storageConditions;
    }

    public function setStorageConditions(?string $storageConditions): void
    {
        $this->storageConditions = $storageConditions;
    }

    public function getStorageType(): ?string
    {
        return $this->storageType;
    }

    public function setStorageType(?string $storageType): void
    {
        $this->storageType = $storageType;
    }

    public function getSellProcedure(): ?string
    {
        return $this->sellProcedure;
    }

    public function setSellProcedure(?string $sellProcedure): void
    {
        $this->sellProcedure = $sellProcedure;
    }

    public function getActualSellProcedure(): ?string
    {
        return $this->actualSellProcedure;
    }

    public function setActualSellProcedure(?string $actualSellProcedure): void
    {
        $this->actualSellProcedure = $actualSellProcedure;
    }

    public function setFullAdditionalName(?string $fullAdditionalName): void
    {
        $this->fullAdditionalName = $fullAdditionalName;
    }

    public function getPersonCategoryId(): ?int
    {
        return $this->personCategoryId;
    }

    public function setPersonCategoryId(?int $personCategoryId): void
    {
        $this->personCategoryId = $personCategoryId;
    }

    public function getPersonCategorySuffixId(): ?int
    {
        return $this->personCategorySuffixId;
    }

    public function setPersonCategorySuffixId(?int $personCategorySuffixId): void
    {
        $this->personCategorySuffixId = $personCategorySuffixId;
    }

    public function getFullFormName(): ?string
    {
        return $this->fullFormName;
    }

    public function setFullFormName(?string $fullFormName): void
    {
        $this->fullFormName = $fullFormName;
    }

    public function getFullFirstWrappingName(): ?string
    {
        return $this->fullFirstWrappingName;
    }

    public function setFullFirstWrappingName(?string $fullFirstWrappingName): void
    {
        $this->fullFirstWrappingName = $fullFirstWrappingName;
    }

    public function getFirstPacking(): ?int
    {
        return $this->firstPacking;
    }

    public function setFirstPacking(?int $firstPacking): void
    {
        $this->firstPacking = $firstPacking;
    }

    public function getSecondPacking(): ?int
    {
        return $this->secondPacking;
    }

    public function setSecondPacking(?int $secondPacking): void
    {
        $this->secondPacking = $secondPacking;
    }

    public function getSuffix(): ?string
    {
        return $this->suffix;
    }

    public function setSuffix(?string $suffix): void
    {
        $this->suffix = $suffix;
    }

    #[JMS\PostDeserialize]
    public function postDeserialize(): void
    {
        $substituteKaisProductIds       = trim($this->substituteProductIds ?? '');
        $this->substituteKaisProductIds = empty($substituteKaisProductIds) ? [] : explode(' ', $substituteKaisProductIds);

        $GTINs           = trim($this->GTINs ?? '');
        $this->kaisGTINs = empty($GTINs) ? [] : explode(' ', $GTINs);

        $this->transliteratedFullTradeName = Transliterator::transliterate($this->fullTradeName, '-');
    }

    public function setWeight(?float $weight): void
    {
        $this->weight = $weight;
    }

    public function setLength(?int $length): void
    {
        $this->length = $length;
    }

    public function setWidth(?int $width): void
    {
        $this->width = $width;
    }

    public function setHeight(?int $height): void
    {
        $this->height = $height;
    }

    public function getIndications(): ?string
    {
        return $this->indications;
    }

    public function setIndications(?string $indications): void
    {
        $this->indications = $indications;
    }

    public function getContraindications(): ?string
    {
        return $this->contraindications;
    }

    public function setContraindications(?string $contraindications): void
    {
        $this->contraindications = $contraindications;
    }

    public function getPharmacodynamics(): ?string
    {
        return $this->pharmacodynamics;
    }

    public function setPharmacodynamics(?string $pharmacodynamics): void
    {
        $this->pharmacodynamics = $pharmacodynamics;
    }

    public function getPharmacokinetics(): ?string
    {
        return $this->pharmacokinetics;
    }

    public function setPharmacokinetics(?string $pharmacokinetics): void
    {
        $this->pharmacokinetics = $pharmacokinetics;
    }

    public function getSideEffects(): ?string
    {
        return $this->sideEffects;
    }

    public function setSideEffects(?string $sideEffects): void
    {
        $this->sideEffects = $sideEffects;
    }

    public function getDosageAndAdministration(): ?string
    {
        return $this->dosageAndAdministration;
    }

    public function setDosageAndAdministration(?string $dosageAndAdministration): void
    {
        $this->dosageAndAdministration = $dosageAndAdministration;
    }

    public function geIngredients(): ?string
    {
        return $this->ingredients;
    }

    public function setIngredients(?string $ingredients): void
    {
        $this->ingredients = $ingredients;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getExpirationDate(): ?int
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?int $expirationDate): void
    {
        $this->expirationDate = $expirationDate;
    }

    public function getPacking(): ?string
    {
        return $this->packing;
    }

    public function setPacking(?string $packing): void
    {
        $this->packing = $packing;
    }

    public function getDose(): ?string
    {
        return $this->dose;
    }

    public function setDose(?string $dose): void
    {
        $this->dose = $dose;
    }

    public function getSimplifiedDosageFormId(): ?int
    {
        return $this->simplifiedDosageFormId;
    }

    public function setSimplifiedDosageFormId(?int $simplifiedDosageFormId): void
    {
        $this->simplifiedDosageFormId = $simplifiedDosageFormId;
    }

    public function isNotForYandexEda(): bool
    {
        return $this->notForYandexEda;
    }

    public function setNotForYandexEda(bool $notForYandexEda): void
    {
        $this->notForYandexEda = $notForYandexEda;
    }
}
