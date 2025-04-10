<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Helper;

class HydrateArray
{
    /**
     * @param array<mixed|null> $values
     * @return array<int|null>|null
     */
    public static function hydrateIntegers(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        return array_map(static fn (string $value): ?int => is_numeric($value) ? (int) $value : null, $values);
    }
}
