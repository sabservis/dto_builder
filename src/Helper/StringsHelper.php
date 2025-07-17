<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Helper;

class StringsHelper
{
    public static function emptyToNull(?string $string): ?string
    {
        if ($string === null) {
            return null;
        }

        if (trim($string) === '') {
            return null;
        }

        return $string;
    }
}
