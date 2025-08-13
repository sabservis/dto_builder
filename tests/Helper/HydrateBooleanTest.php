<?php

namespace SabServis\DTOBuilder\Tests\Helper;

use PHPUnit\Framework\TestCase;
use SabServis\DTOBuilder\Helper\HydrateBoolean;
use SabServis\DTOBuilder\Tests\DTO\Utils\YesNoCZEnum;

class HydrateBooleanTest extends TestCase
{

    public function testHydrateTextNumberOne()
    {
        $result = HydrateBoolean::hydrate('1');
        $this->assertEquals(true, $result);
    }

    public function testHydrateTextNumberZero()
    {
        $result = HydrateBoolean::hydrate('0');
        $this->assertEquals(false, $result);
    }

    public function testHydrateIntegerNumberOne()
    {
        $result = HydrateBoolean::hydrate(1);
        $this->assertEquals(true, $result);
    }

    public function testHydrateIntegerNumberZero()
    {
        $result = HydrateBoolean::hydrate(0);
        $this->assertEquals(false, $result);
    }

    public function testHydrateBoolTrue()
    {
        $result = HydrateBoolean::hydrate('true');
        $this->assertEquals(true, $result);
    }

    public function testHydrateTBoolFalse()
    {
        $result = HydrateBoolean::hydrate('false');
        $this->assertEquals(false, $result);
    }

    public function testHydrateEnumTrue()
    {
        $result = HydrateBoolean::hydrateFromYesNoEnum(YesNoCZEnum::Yes);
        $this->assertEquals(true, $result);
    }

    public function testHydrateEnumFalse()
    {
        $result = HydrateBoolean::hydrateFromYesNoEnum(YesNoCZEnum::No);
        $this->assertEquals(false, $result);
    }

}
