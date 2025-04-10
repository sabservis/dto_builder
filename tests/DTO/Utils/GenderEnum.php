<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Tests\DTO\Utils;

enum GenderEnum: string
{
    /**
     * Represents the gender of a person as male.
     * Muž
     */
    case Male = 'M';

    /**
     * Represents the gender of a person as female.
     * Žena
     */
    case Female = 'F';
}
