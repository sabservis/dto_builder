<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Helper;

final class FloatHelper
{
    public static function getFloatOrNull(mixed $value): ?float
    {
        if (is_string($value)) {
            $value = trim($value);
        }

        if (is_numeric($value) === false) {
            return null;
        }

        return (float) $value;
    }
}
