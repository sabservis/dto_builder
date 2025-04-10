<?php

namespace SabServis\DTOBuilder\Tests\Helper;

use SabServis\DTOBuilder\DTO\Builder\DTOBuilder;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use SabServis\DTOBuilder\Helper\HydrateBoolean;

class HydrateBooleanTest extends TestCase
{

    private DTOBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = $this->createMock(DTOBuilder::class);
    }

    private function getReflectionParameter($index = 0)
    {
        $reflectionMethod = new ReflectionMethod(SomeClass::class, 'someMethod');

        return $reflectionMethod->getParameters()[$index];
    }

    public function testHydrateTextNumberOne()
    {
        $reflectionParameter = $this->getReflectionParameter();
        $result = HydrateBoolean::hydrate('1', $this->builder, $reflectionParameter);
        $this->assertEquals(true, $result);
    }

    public function testHydrateTextNumberZero()
    {
        $reflectionParameter = $this->getReflectionParameter();
        $result = HydrateBoolean::hydrate('0', $this->builder, $reflectionParameter);
        $this->assertEquals(false, $result);
    }

    public function testHydrateIntegerNumberOne()
    {
        $reflectionParameter = $this->getReflectionParameter();
        $result = HydrateBoolean::hydrate(1, $this->builder, $reflectionParameter);
        $this->assertEquals(true, $result);
    }

    public function testHydrateIntegerNumberZero()
    {
        $reflectionParameter = $this->getReflectionParameter();
        $result = HydrateBoolean::hydrate(0, $this->builder, $reflectionParameter);
        $this->assertEquals(false, $result);
    }

    public function testHydrateBoolTrue()
    {
        $reflectionParameter = $this->getReflectionParameter();
        $result = HydrateBoolean::hydrate('true', $this->builder, $reflectionParameter);
        $this->assertEquals(true, $result);
    }

    public function testHydrateTBoolFalse()
    {
        $reflectionParameter = $this->getReflectionParameter();
        $result = HydrateBoolean::hydrate('false', $this->builder, $reflectionParameter);
        $this->assertEquals(false, $result);
    }

}

class SomeClass
{

    public function someMethod(bool $param1, bool $param2)
    {
    }

}
