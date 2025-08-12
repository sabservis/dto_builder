<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Enum;

enum DateTimeFormatEnum: string
{
    case DateTime = 'c';
    case Date = 'Y-m-d';
    case Time = 'H:i:s';

    public function mysqlFormat(): string
    {
        return match ($this) {
            self::DateTime => '%Y-%m-%dT%T',
            self::Date => '%Y-%m-%d',
            self::Time => '%T',
        };
    }
}
