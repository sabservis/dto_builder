<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Attribute;

use Attribute;
use SabServis\DTOBuilder\Enum\DateTimeFormatEnum;

#[Attribute(Attribute::TARGET_PROPERTY)]
class HydrateToDateTime
{
    public function __construct(
        public string $dateTimeClass = \DateTime::class,
    ) {
    }
}
