<?php

namespace App\Tests;

use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;
use App\OffersApi\Domain\Entity\Offer\Offer;

abstract class AbstractControllerTestCase extends TestCase
{
    /** @return array<int, Offer> */
    public function formatResponse(mixed $response): array
    {
        $context = SerializationContext::create()->setSerializeNull(true);

        $serializerBuilder = SerializerBuilder::create();
        $serializerBuilder->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy());

        return json_decode(
            $serializerBuilder->build()->serialize($response, 'json', $context),
            true,
        );
    }
}
