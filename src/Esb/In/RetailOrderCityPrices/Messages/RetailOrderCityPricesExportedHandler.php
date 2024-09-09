<?php

declare(strict_types=1);

namespace App\Esb\In\RetailOrderCityPrices\Messages;

use App\Esb\In\RetailOrderCityPrices\Commands\ImportOrderPrice;
use App\Esb\In\RetailOrderCityPrices\Infrastructure\OrderPricesFtpAccessObject;
use App\OffersBuilding\Commands\BuildOffersByKladrId;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @codeCoverageIgnore
 */
final class RetailOrderCityPricesExportedHandler
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly OrderPricesFtpAccessObject $dao,
    ) {}

    public function handle(RetailOrderCityPricesMessage $message): void
    {
        $orderPrice = $this->dao->download($message->orderPricesData);

        if (filesize($orderPrice) > 1) {
            $this->messageBus->dispatch(new ImportOrderPrice($message->cityKladrId, $orderPrice));
            $this->messageBus->dispatch(new BuildOffersByKladrId($message->cityKladrId));
        }
    }
}
