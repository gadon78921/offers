<?php

declare(strict_types=1);

namespace App\Tests\OffersApi\Domain\Entity\Offer;

use App\OffersApi\Domain\Entity\Offer\OfferAmountInStorage;
use App\Tests\OffersApi\DataHelperCase;
use PHPUnit\Framework\TestCase;

final class OfferAmountInStorageTest extends TestCase
{
    public function testOfferAmountInStorage(): void
    {
        $sut = OfferAmountInStorage::createFromQuantityProduct(
            DataHelperCase::getQuantityProudct(),
        );

        self::assertSame($sut->productId, 15099);
        self::assertSame($sut->amount, 10);
        self::assertSame($sut->amountUnpacked, 4);
    }
}
