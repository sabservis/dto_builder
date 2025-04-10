<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Attribute;

use Attribute;

/**
 * Class HydrateColumn
 *
 * This class represents an attribute used to mark a property as a column to be hydrated from a data source.
 *
 * The HydrateColumn attribute should be applied to class properties only.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class HydrateColumn
{
    public function __construct(
        public string $name,
        public ?string $arrayTarget = null,
    ) {
    }
}
