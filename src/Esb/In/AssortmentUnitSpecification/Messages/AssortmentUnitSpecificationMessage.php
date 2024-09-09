<?php

declare(strict_types=1);

namespace App\Esb\In\AssortmentUnitSpecification\Messages;

use JMS\Serializer\Annotation as JMS;
use Monastirevrf\EsbBundle\Messages\EsbMessageInterface;
use Monastirevrf\EsbBundle\Messages\EsbMessageTrait;

class AssortmentUnitSpecificationMessage implements EsbMessageInterface
{
    use EsbMessageTrait;

    /** @param array<int, AttributeNode> $attributes */
    public function __construct(
        #[JMS\SerializedName('id')]
        public readonly int $assortmentUnitId,
        public readonly bool $deleted,
        #[JMS\XmlList(entry: 'attributes', inline: true)]
        #[JMS\Type('array<App\Esb\In\AssortmentUnitSpecification\Messages\AttributeNode>')]
        private readonly array $attributes,
    ) {}

    /** @return array<mixed> */
    public function attributes(): array
    {
        $attributes = [];

        foreach ($this->attributes as $attribute) {
            $attribute->vars->forAll(function (int $key, VarNode $var) use (&$attributes) {
                $attributes[$var->name] = $var->value();

                return true;
            });
        }

        return $attributes;
    }
}
