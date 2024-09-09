<?php

declare(strict_types=1);

namespace App\Esb\In\AssortmentUnitSpecification\Messages;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;

final class AttributeNode
{
    /** @param ArrayCollection<int, VarNode> $vars */
    public function __construct(
        #[JMS\XmlList(entry: 'var', inline: true)]
        #[JMS\Type('ArrayCollection<App\Esb\In\AssortmentUnitSpecification\Messages\VarNode>')]
        public readonly ArrayCollection $vars,
    ) {}
}
