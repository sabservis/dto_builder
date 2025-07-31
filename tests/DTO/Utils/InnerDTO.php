<?php

namespace SabServis\DTOBuilder\Tests\DTO\Utils;

use SabServis\DTOBuilder\Attribute\HydrateColumn;
use SabServis\DTOBuilder\Attribute\HydrateFromFunction;
use SabServis\DTOBuilder\DTO\AbstractDTO;
use SabServis\DTOBuilder\Tests\DTO\Utils;

class InnerDTO extends AbstractDTO
{
    public function __construct(
        #[HydrateColumn(name: 'object.name')]
        public string $name,

        #[HydrateColumn(name: 'object.age')]
        public ?int $age,

        #[HydrateColumn(name: 'object.value')]
        #[HydrateFromFunction(functionName: [Utils\OuterDTO::class, 'hydrateData'])]
        public int $value,
    ) {
    }
}
