<?php

declare(strict_types=1);

namespace App\Esb\In\AssortmentUnitSpecification;

use App\Esb\In\AssortmentUnitSpecification\Infrastructure\AssortmentUnitSpecificationAccessObject;
use App\Esb\In\AssortmentUnitSpecification\Infrastructure\AssortmentUnitSpecificationTransferObject;
use App\Esb\In\AssortmentUnitSpecification\Messages\AssortmentUnitSpecificationMessage;
use App\FiltersBuilding\Commands\BuildFiltersByAssortmentUnitId;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @codeCoverageIgnore
 */
final class AssortmentUnitSpecificationHandler
{
    public function __construct(
        private readonly AssortmentUnitSpecificationAccessObject $dao,
        private readonly MessageBusInterface $messageBus
    ) {}

    public function handle(AssortmentUnitSpecificationMessage $message): void
    {
        if ($message->deleted) {
            $this->dao->remove($message->assortmentUnitId);

            return;
        }

        $assortmentUnitSpecification = $this->handleAttributes($message);

        $this->dao->save(new AssortmentUnitSpecificationTransferObject(
            $assortmentUnitSpecification['assortmentUnitId'],
            $assortmentUnitSpecification['baseProductId'],
            $assortmentUnitSpecification['packageQuantity'],
            $assortmentUnitSpecification['prescriptionForm'],
            $assortmentUnitSpecification['primaryPackageQuantity'],
            $assortmentUnitSpecification['secondaryPackageQuantity'],
            $assortmentUnitSpecification['isVital'] ? 'true' : 'false',
            $assortmentUnitSpecification['isStorageTypeCold'] ? 'true' : 'false',
            $assortmentUnitSpecification['canUnpackPrimary'] ? 'true' : 'false',
            $assortmentUnitSpecification['canUnpackSecondary'] ? 'true' : 'false',
            $assortmentUnitSpecification['tradeName'],
            $assortmentUnitSpecification['fullTradeName'] ?? '',
            $assortmentUnitSpecification['subtitle'],
            $assortmentUnitSpecification['manufacturerName'],
            $assortmentUnitSpecification['manufacturerCountryName'],
            $assortmentUnitSpecification['activeSubstance'] ?? null,
            $assortmentUnitSpecification['dosage'] ?? null,
            $assortmentUnitSpecification['dosageForm'] ?? null,
            $assortmentUnitSpecification['sellProcedure'] ?? null,
            $assortmentUnitSpecification['brand'] ?? null,
            $assortmentUnitSpecification['description'] ?? null,
            $assortmentUnitSpecification['composition'] ?? null,
            $assortmentUnitSpecification['indicationsForUse'] ?? null,
            $assortmentUnitSpecification['contraindicationsForUse'] ?? null,
            $assortmentUnitSpecification['pharmokinetic'] ?? null,
            $assortmentUnitSpecification['pharmodynamic'] ?? null,
            $assortmentUnitSpecification['methodOfUse'] ?? null,
            $assortmentUnitSpecification['sideEffect'] ?? null,
            $assortmentUnitSpecification['storageConditions'] ?? null,
            $assortmentUnitSpecification['wrappingName'] ?? null,
            $assortmentUnitSpecification['expirationDate'] ?? null,
            $assortmentUnitSpecification['numberOfDoses'] ?? null,
        ));

        $this->messageBus->dispatch(new BuildFiltersByAssortmentUnitId($assortmentUnitSpecification['assortmentUnitId']));
    }

    /** @return array<mixed> */
    private function handleAttributes(AssortmentUnitSpecificationMessage $message): array
    {
        return array_merge(
            [
                'assortmentUnitId' => $message->assortmentUnitId,
            ],
            $message->attributes()
        );
    }
}
