<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Tests\Helper;

use PHPUnit\Framework\TestCase;
use SabServis\DTOBuilder\Helper\IntegerHelper;

class IntegerHelperTest extends TestCase
{
    public function testIntOrNull(): void
    {
        $this->assertEquals(1, IntegerHelper::getIntegerOrNull('1'));
        $this->assertEquals(1800, IntegerHelper::getIntegerOrNull('1 800'));
        $this->assertEquals(1, IntegerHelper::getIntegerOrNull(' 1 '));
        $this->assertEquals(15, IntegerHelper::getIntegerOrNull('15.6'));
        $this->assertEquals(15, IntegerHelper::getIntegerOrNull(15.6));
        $this->assertEquals(15, IntegerHelper::getIntegerOrNull(15));
        $this->assertEquals(null, IntegerHelper::getIntegerOrNull('hi'));
        $this->assertEquals(null, IntegerHelper::getIntegerOrNull(''));
    }
}
