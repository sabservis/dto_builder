<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Helper;

final class IntegerHelper
{
    public static function getIntegerOrNull(mixed $value): ?int
    {
        if (is_string($value)) {
            $value = trim($value);
            $value = str_replace(' ', '', $value);
        }

        if (is_numeric($value) === false) {
            return null;
        }

        return (int) $value;
    }
}
