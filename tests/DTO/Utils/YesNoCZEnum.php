<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Tests\DTO\Utils;

use SabServis\DTOBuilder\Enum\YesNoEnumInterface;

enum YesNoCZEnum: string implements YesNoEnumInterface
{
    case Yes = 'ano';
    case No = 'ne';

    public function toBoolean(): bool
    {
        return $this->value === self::yesValue()->value;
    }

    public static function fromBoolean(bool $value): static
    {
        return $value ? self::Yes : self::No;
    }

    public static function yesValue(): static
    {
        return self::Yes;
    }

    public static function noValue(): static
    {
        return self::No;
    }
}
