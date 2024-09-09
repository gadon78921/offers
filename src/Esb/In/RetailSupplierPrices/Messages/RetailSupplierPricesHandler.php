<?php

declare(strict_types=1);

namespace App\Esb\In\RetailSupplierPrices\Messages;

use App\Esb\In\RetailSupplierPrices\Commands\ImportSupplierPrice;
use App\Esb\In\RetailSupplierPrices\Infrastructure\SupplierPricesDataFtpAccessObject;
use App\Esb\In\RetailTradePoint\RetailTradePointRepository;
use App\OffersBuilding\Commands\BuildOffersByTradePointIds;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @codeCoverageIgnore
 */
final class RetailSupplierPricesHandler
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly SupplierPricesDataFtpAccessObject $dao,
        private readonly RetailTradePointRepository $tradePointRepository,
    ) {}

    public function handle(RetailSupplierPricesMessage $message): void
    {
        $supplierPrice = $this->dao->download($message->supplierPricesData);

        if (filesize($supplierPrice) > 1) {
            $this->messageBus->dispatch(new ImportSupplierPrice($message->supplierId, $supplierPrice));

            $tradePointIdsByKladrId = $this->tradePointRepository->getTradePointIdsBySupplierId($message->supplierId);

            foreach ($tradePointIdsByKladrId as $tradePointIds) {
                $this->messageBus->dispatch(new BuildOffersByTradePointIds($tradePointIds));
            }
        }
    }
}
