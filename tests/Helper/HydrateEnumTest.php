<?php

namespace SabServis\DTOBuilder\Tests\Helper;

use SabServis\DTOBuilder\DTO\Builder\DTOBuilder;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorParameter;
use SabServis\DTOBuilder\Exception\DTOValidationException;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use SabServis\DTOBuilder\Helper\HydrateEnum;

class HydrateEnumTest extends TestCase
{
    private DTOBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = $this->createMock(DTOBuilder::class);
    }

    private function getReflectionParameter($index = 0)
    {
        $reflectionMethod = new ReflectionMethod(SomeClass2::class, 'someMethod');
        return new DTOBuilderConstructorParameter($reflectionMethod->getParameters()[$index]);
    }

    public function testHydrateEnumValidValue()
    {
        $reflectionParameter = $this->getReflectionParameter();
        $result = HydrateEnum::hydrateEnum(ExampleEnum::VALUE_ONE->value, $reflectionParameter);
        $this->assertEquals(ExampleEnum::VALUE_ONE, $result);
    }

    public function testHydrateEnumInvalidValue()
    {
        $this->expectException(DTOValidationException::class);

        $reflectionParameter = $this->getReflectionParameter();
        HydrateEnum::hydrateEnum('invalid', $reflectionParameter);
    }

    public function testHydrateEnumNullValueAllowed()
    {
        //$reflectionParameter = $this->getMockReflectionParameter(ExampleEnum::class, true);
        $reflectionParameter = $this->getReflectionParameter(1);
        $result = HydrateEnum::hydrateEnum(null, $reflectionParameter);
        $this->assertNull($result);
    }

    public function testHydrateEnumNullValueNotAllowed()
    {
        $this->expectException(DTOValidationException::class);

        $reflectionParameter = $this->getReflectionParameter();
        HydrateEnum::hydrateEnum(null, $reflectionParameter);
    }

    public function testHydrateEnumByNameValidName()
    {
        //$reflectionParameter = new ReflectionParameter([ExampleEnum::class, 'cases'], 'value');
        $reflectionParameter = $this->getReflectionParameter();
        $result = HydrateEnum::hydrateEnumByName('VALUE_ONE', $reflectionParameter);
        $this->assertEquals(ExampleEnum::VALUE_ONE, $result);
    }

    public function testHydrateEnumByNameInvalidName()
    {
        $this->expectException(DTOValidationException::class);

        $reflectionParameter = $this->getReflectionParameter();
        //$reflectionParameter = new ReflectionParameter([ExampleEnum::class, 'cases'], 'value');
        HydrateEnum::hydrateEnumByName('NON_EXIST', $reflectionParameter);
    }

}


class SomeClass2
{
    public static function someMethod(
        ExampleEnum $notNullParam,
        ?ExampleEnum $nullableParam,
    ) {
        // some implementation
    }
}

enum ExampleEnum: string
{
    case VALUE_ONE = 'One';
    case VALUE_TWO = 'Two';
}
