<?php

namespace SabServis\DTOBuilder\Tests\DTO;

use SabServis\DTOBuilder\DTO\Builder\Filter\DTONumberValueFilter;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorParameter;
use SabServis\DTOBuilder\Exception\DTOCreationException;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;

class DTONumberValueFilterTest extends TestCase
{

    private DTONumberValueFilter $filter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filter = new DTONumberValueFilter();
    }

    public function testFilterWithValidInt()
    {
        $parameter = new DTOBuilderConstructorParameter(new ReflectionParameter([TestClass1::class, 'setNumber'], 'number'));
        $result = $this->filter->filter("123", $parameter, "number", true);

        $this->assertIsInt($result);
        $this->assertEquals(123, $result);
    }

    public function testFilterWithValidFloat()
    {
        $parameter =  new DTOBuilderConstructorParameter(new ReflectionParameter([TestClass1::class, 'setFloat'], 'number'));
        $result = $this->filter->filter("123.456", $parameter, "number", true);

        $this->assertIsFloat($result);
        $this->assertEquals(123.456, $result);
    }

    public function testFilterWithInvalidNumber()
    {
        $this->expectException(DTOCreationException::class);
        $parameter =  new DTOBuilderConstructorParameter(new ReflectionParameter([TestClass1::class, 'setNumber'], 'number'));

        $this->filter->filter("abc", $parameter, "number", true);
    }

    public function testFilterWithUnrelatedType()
    {
        $parameter =  new DTOBuilderConstructorParameter(new ReflectionParameter([TestClass1::class, 'setString'], 'text'));
        $result = $this->filter->filter("string", $parameter, "text", true);

        $this->assertEquals("string", $result);
    }

}

class TestClass1
{
    public function setNumber(int $number) {}
    public function setFloat(float $number) {}
    public function setString(string $text) {}
}
