<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class HydrateFromFunction
{
    /**
     * @param array<string> $functionName
     */
    public function __construct(
        public array $functionName,
        public bool $callOnlyWithPayload = false,
    ) {
    }
}
