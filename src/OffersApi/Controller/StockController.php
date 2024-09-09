<?php

declare(strict_types=1);

namespace App\OffersApi\Controller;

use App\OffersApi\Repository\Products\ProductsInTradePointsDatabaseAccessObject;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class StockController extends AbstractController
{
    /**
     * @return list<array{'id': int, 'qty': int}>
     */
    #[Route('/stock/get-by-tradepoint', name: 'stock-tradepoint', methods: 'POST')]
    #[ParamConverter('tradePointId', options: ['validators' => ['NotNull' => ['message' => 'Это поле обязательно']]], converter: 'json.request_body_param_fetcher')]
    #[ParamConverter('lastSyncDateTime', converter: 'json.request_body_param_fetcher')]
    public function index(int $tradePointId, ?string $lastSyncDateTime, ProductsInTradePointsDatabaseAccessObject $dao): array
    {
        return $dao->fetchProductsStocks(tradePointId: $tradePointId, lastSyncDateTime: $lastSyncDateTime);
    }
}
