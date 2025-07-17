<?php

namespace SabServis\DTOBuilder\Tests\Helper;

use PHPUnit\Framework\TestCase;
use SabServis\DTOBuilder\Helper\FloatHelper;
use SabServis\DTOBuilder\Helper\StringsHelper;

class StringsHelperTest extends TestCase
{
    public function testFloatOrNull(): void
    {
        $this->assertEquals('aaaa', StringsHelper::emptyToNull('aaaa'));
        $this->assertEquals(' 1 ', StringsHelper::emptyToNull(' 1 '));
        $this->assertEquals('15.6', StringsHelper::emptyToNull('15.6'));
        $this->assertEquals(null, StringsHelper::emptyToNull(null));
        $this->assertEquals(null, StringsHelper::emptyToNull(''));
    }
}
