<?php

namespace SabServis\DTOBuilder\Tests\Helper;

use PHPUnit\Framework\TestCase;
use SabServis\DTOBuilder\Helper\FloatHelper;

class FloatHelperTest extends TestCase
{
    public function testFloatOrNull(): void
    {
        $this->assertEquals(1.0, FloatHelper::getFloatOrNull('1'));
        $this->assertEquals(1.0, FloatHelper::getFloatOrNull(' 1 '));
        $this->assertEquals(15.6, FloatHelper::getFloatOrNull('15.6'));
        $this->assertEquals(15.6, FloatHelper::getFloatOrNull(15.6));
        $this->assertEquals(15.0, FloatHelper::getFloatOrNull(15));
        $this->assertEquals(null, FloatHelper::getFloatOrNull('hi'));
        $this->assertEquals(null, FloatHelper::getFloatOrNull(''));
    }
}
