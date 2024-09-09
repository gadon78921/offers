<?php

declare(strict_types=1);

namespace App\Esb\In\RetailTradePoint;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Monastirevrf\EsbBundle\Messages\EsbMessageInterface;
use Monastirevrf\EsbBundle\Messages\EsbMessageTrait;

#[ORM\Table(name: 'retail_tradepoints')]
#[ORM\Entity]
class RetailTradePointMessage implements EsbMessageInterface
{
    use EsbMessageTrait;

    #[ORM\Id]
    #[ORM\Column]
    #[JMS\SerializedName('id')]
    private int $tradePointId;

    #[ORM\Column]
    private ?string $kladrId = null;

    #[ORM\Column]
    #[JMS\SerializedName('providesDelivery')]
    private bool $deliveryAvailable;

    /**
     * Column(type="text[]").
     *
     * @var array{string} $firmListIds
     */
    #[ORM\Column(type: 'text[]')]
    private array $firmListIds;

    /**
     * Column(type="text[]").
     *
     * @var array{string} $supplierListIds
     */
    #[ORM\Column(type: 'text[]')]
    private array $supplierListIds;

    private bool $deleted;
    private ?string $firmIds;
    private ?string $supplierIds; // @phpstan-ignore-line

    #[JMS\SerializedName('cityId')]
    private string $retailCityId;

    public function getTradePointId(): int
    {
        return $this->tradePointId;
    }

    public function setTradePointId(int $tradePointId): void
    {
        $this->tradePointId = $tradePointId;
    }

    public function getKladrId(): ?string
    {
        return $this->kladrId;
    }

    public function setKladrId(?string $kladrId): void
    {
        $this->kladrId = $kladrId;
    }

    public function getDeliveryAvailable(): bool
    {
        return $this->deliveryAvailable;
    }

    public function setDeliveryAvailable(bool $deliveryAvailable): void
    {
        $this->deliveryAvailable = $deliveryAvailable;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    /** @return array{string} */
    public function getFirmListIds(): array
    {
        return $this->firmListIds;
    }

    /** @param array{string} $firmListIds */
    public function setFirmListIds(array $firmListIds): void
    {
        $this->firmListIds = $firmListIds;
    }

    /** @return array{string} */
    public function getSupplierListIds(): array
    {
        return $this->supplierListIds;
    }

    /** @param array{string} $supplierListIds */
    public function setSupplierListIds(array $supplierListIds): void
    {
        $this->supplierListIds = $supplierListIds;
    }

    public function getFirmIds(): ?string
    {
        return $this->firmIds;
    }

    public function getSetFirmIds(?string $firmIds): void
    {
        $this->firmIds = $firmIds;
    }

    public function getRetailCityId(): string
    {
        return $this->retailCityId;
    }

    public function setRetailCityId(string $retailCityId): void
    {
        $this->retailCityId = $retailCityId;
    }

    #[JMS\PostDeserialize]
    public function postDeserialize(): void
    {
        $this->setFirmListIds($this->convertFirmIds());
        $this->setSupplierListIds($this->convertSupplierIds());
    }

    /** @return array{string} */
    private function convertFirmIds(): array
    {
        $firmIds = trim($this->firmIds ?? '');

        return empty($firmIds) ? [] : explode(' ', $firmIds);
    }

    /** @return array{string} */
    private function convertSupplierIds(): array
    {
        $supplierIds = trim($this->supplierIds ?? '');

        return empty($supplierIds) ? [] : explode(' ', $supplierIds);
    }
}
