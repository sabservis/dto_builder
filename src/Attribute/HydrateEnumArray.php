<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class HydrateEnumArray
{
    public function __construct(
        public string $class,
    ) {
    }
}
