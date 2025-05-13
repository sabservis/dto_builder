<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Tests\DTO;


use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use SabServis\DTOBuilder\Attribute\HydrateString;
use SabServis\DTOBuilder\DTO\Builder\Filter\DTOStringValueFilter;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorMethod;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorParameter;

class DTOStringValueFilterTest extends TestCase
{

    private DTOStringValueFilter $filter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filter = new DTOStringValueFilter();
    }

    public function testFilterFromInt(): void
    {
        $parameter = $this->getParameter('stringFromInt');
        $result = $this->filter->filter(123, $parameter, true);
        $this->assertEquals('123', $result);
    }

    public function testFilterFromFloat(): void
    {
        $parameter = $this->getParameter('stringFromFloat');
        $result = $this->filter->filter(123.5, $parameter, true);
        $this->assertEquals('123.5', $result);
    }

    public function testFilterFromStringable(): void
    {
        $parameter = $this->getParameter('stringFromStringable');
        $value = \Mockery::mock(\Stringable::class);
        $value->shouldReceive('__toString')->andReturn('stringable');

        $result = $this->filter->filter($value, $parameter, true);
        $this->assertEquals('stringable', $result);
    }

    private function getParameter(string $name): DTOBuilderConstructorParameter
    {
        $method = new DTOBuilderConstructorMethod(new ReflectionMethod(TestClass4::class, '__construct'));
        return $method->getParameters()[$name];
    }
}

class TestClass4
{
    public function __construct(
        #[HydrateString()]
        public string $stringFromInt,

        #[HydrateString()]
        public string $stringFromFloat,

        #[HydrateString()]
        public string $stringFromStringable,
    ) {
    }


}
