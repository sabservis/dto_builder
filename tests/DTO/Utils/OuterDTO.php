<?php

namespace SabServis\DTOBuilder\Tests\DTO\Utils;

use SabServis\DTOBuilder\Attribute\HydrateColumn;
use SabServis\DTOBuilder\Attribute\HydrateDateTime;
use SabServis\DTOBuilder\Attribute\HydrateFromFunction;
use SabServis\DTOBuilder\Attribute\HydrateString;
use SabServis\DTOBuilder\Attribute\HydrateToDateTime;
use SabServis\DTOBuilder\DTO\AbstractDTO;
use SabServis\DTOBuilder\Enum\DateTimeFormatEnum;
use SabServis\DTOBuilder\Helper\HydrateEnum;

class OuterDTO extends AbstractDTO
{
    public function __construct(
        public string $name,

        public int $age,

        #[HydrateFromFunction(functionName: [HydrateEnum::class, 'hydrateEnum'])]
        public GenderEnum $gender,

        #[HydrateColumn(name: 'innerObject')]
        public InnerDTO $innerDTO,

        #[HydrateColumn(name: 'items', arrayTarget: InnerDTO::class)]
        public array $innerDTOs,

        #[HydrateDateTime(format: DateTimeFormatEnum::DateTime)]
        public string $dateTime,

        #[HydrateDateTime(format: DateTimeFormatEnum::Date)]
        public string $date,

        #[HydrateDateTime(format: DateTimeFormatEnum::Time)]
        public string $time,

        #[HydrateToDateTime()]
        public \DateTime $stringDate,

        #[HydrateToDateTime(dateTimeClass: \DateTimeImmutable::class)]
        public \DateTimeImmutable $stringDateTime,

        #[HydrateFromFunction(functionName: [self::class, 'hydrateData'])]
        public int $valueFromFunction,

        #[HydrateFromFunction(functionName: [self::class, 'hydrateToday'])]
        public string $todayFromFunction,

        #[HydrateString()]
        public string $stringFromInt,

        #[HydrateString()]
        public string $stringFromFloat,

        #[HydrateString()]
        public string $stringFromStringable,

        public string $requiredParamWithDefault = 'Value1',

        public ?string $optionalParamWithDefault = 'Value2',
    ) {
    }

    public static function hydrateData(mixed $value): int {
        return $value + 5;
    }

    public static function hydrateToday(mixed $value, \DateTime $today): string {
        return $today->format('Y-m-d');
    }
}
