<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Attribute;

use Attribute;
use SabServis\DTOBuilder\Enum\DateTimeFormatEnum;

#[Attribute(Attribute::TARGET_PROPERTY)]
class HydrateDateTime
{
    public function __construct(
        public DateTimeFormatEnum $format = DateTimeFormatEnum::DateTime,
    ) {
    }
}
