<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Enum;

/**
 * Interface YesNoEnumInterface
 * @used-by \BackedEnum
 */
interface YesNoEnumInterface
{
    public function toBoolean(): bool;

    public static function yesValue(): \BackedEnum;

    public static function noValue(): \BackedEnum;

    public static function fromBoolean(bool $value): static;
}
