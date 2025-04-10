<?php

namespace SabServis\DTOBuilder\Tests\DTO\Utils;

use SabServis\DTOBuilder\Attribute\HydrateColumn;
use SabServis\DTOBuilder\Attribute\HydrateDateTime;
use SabServis\DTOBuilder\Attribute\HydrateFromFunction;
use SabServis\DTOBuilder\DTO\AbstractDTO;
use SabServis\DTOBuilder\Enum\DateTimeFormatEnum;
use SabServis\DTOBuilder\Helper\HydrateEnum;
use Symfony\Component\Validator\Constraints as Assert;

class TestDTO extends AbstractDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public int $notNullInt,

        #[Assert\NotBlank]
        public string $notNullString,

        #[Assert\NotBlank]
        #[HydrateColumn(name: 'otherColumn')]
        public string $anotherColumn,

        public ?int $nullableInt,

        #[Assert\NotBlank]
        #[HydrateFromFunction(functionName: [HydrateEnum::class, 'hydrateEnum'])]
        public GenderEnum $enum,

        #[Assert\NotBlank]
        #[HydrateDateTime]
        public string $datetime,

        #[Assert\NotBlank]
        #[HydrateDateTime(format: DateTimeFormatEnum::Date)]
        public string $date,

        #[Assert\NotBlank]
        #[HydrateDateTime(format: DateTimeFormatEnum::Time)]
        public string $time,

        public ?bool $nullableBoolean,
    ) {
    }
}
