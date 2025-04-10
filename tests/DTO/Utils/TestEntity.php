<?php

namespace SabServis\DTOBuilder\Tests\DTO\Utils;

use DateTime;

class TestEntity
{
    public function __construct(
        public int      $notNullInt,
        public string   $notNullString,
        public string   $otherColumn,
        public string   $enum,
        public DateTime $datetime,
        public DateTime $date,
        public DateTime $time,
        public ?int     $nullableInt = null,
        public ?bool    $nullableBoolean = null,
    ) {
    }
}
