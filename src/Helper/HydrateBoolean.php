<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Helper;

use SabServis\DTOBuilder\Enum\YesNoEnumInterface;

class HydrateBoolean
{
    /**
     * Hydrates boolean by value
     */
    public static function hydrate(mixed $value): mixed
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Hydrates boolean by value
     */
    public static function hydrateNullable(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Hydrates boolean by enum that implements YesNoInterface
     * @param \BackedEnum $value
     */
    public static function hydrateFromYesNoEnum(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        /** @var class-string<YesNoEnumInterface> $enumClass */
        $enumClass = $value::class;

        if (!$value instanceof YesNoEnumInterface) {
            throw new \UnexpectedValueException($enumClass . ' must implements YesNoEnumInterface');
        }

        if (!$value instanceof \BackedEnum) {
            throw new \UnexpectedValueException($enumClass . ' must be Enum');
        }

        return $value === $enumClass::yesValue();
    }
}
