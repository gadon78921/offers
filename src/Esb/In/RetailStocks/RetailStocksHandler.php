<?php

declare(strict_types=1);

namespace App\Esb\In\RetailStocks;

use App\Esb\In\RetailStocks\Dto\RetailStocksCollection;
use App\Esb\In\RetailStocks\Dto\RetailStocksDTO;
use App\Esb\In\RetailStocks\Messages\RetailStockRow;
use App\Esb\In\RetailStocks\Messages\RetailStocksMessage;
use App\Esb\In\RetailTradePoint\RetailTradePointRepository;
use App\OffersBuilding\Commands\BuildOffersByKaisProductIdsAndTradePointId;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @codeCoverageIgnore
 */
final class RetailStocksHandler
{
    public function __construct(
        private readonly RetailStocksAccessObject $dao,
        private readonly RetailTradePointRepository $tradePointRepository,
        private readonly MessageBusInterface $messageBus,
    ) {}

    public function handle(RetailStocksMessage $message): void
    {
        $retailStocksCollection = $this->createRetailStocksDTOCollection($message);
        $this->dao->saveBulk($retailStocksCollection);

        $tradePointIds = $this->tradePointRepository->getTradePointIdsByFirmId($message->firmSubdivisionId);

        foreach ($tradePointIds as $tradePointId) {
            $this->messageBus->dispatch(new BuildOffersByKaisProductIdsAndTradePointId($retailStocksCollection->getKeys(), $tradePointId));
        }
    }

    private function createRetailStocksDTOCollection(RetailStocksMessage $message): RetailStocksCollection
    {
        $rows                   = $message->rows->retailStockRows->filter(fn(RetailStockRow $row) => false === $row->isDiscount);
        $retailStocksCollection = new RetailStocksCollection();

        $rows->map(function (RetailStockRow $row) use ($retailStocksCollection, $message) {
            $retailStocksCollection->set($row->kaisProductId, new RetailStocksDTO(
                $row->kaisProductId,
                $message->firmSubdivisionId,
                $message->storeId,
                $row->freeQty,
                $row->dividedFreeQty,
                $row->retailPriceWithTax,
                $row->avgIncomePriceWithTax
            ));
        });

        return $retailStocksCollection;
    }
}
