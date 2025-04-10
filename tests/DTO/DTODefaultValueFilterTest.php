<?php

namespace SabServis\DTOBuilder\Tests\DTO;

use SabServis\DTOBuilder\DTO\Builder\Filter\DTODefaultValueFilter;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorMethod;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorParameter;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionParameter;

/**
 *  When no value provided   - use default value if available
 *  When null value provided - use default value if available and param doesn't accept null
 */
class DTODefaultValueFilterTest extends TestCase
{

    private DTODefaultValueFilter $filter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filter = new DTODefaultValueFilter();
    }

    public function testFilterWithProvidedNullRequiredParam()
    {
        $parameter = $this->getParameter('requiredParam');

        $result = $this->filter->filter(null, $parameter, true);
        $this->assertEquals('Value1', $result);
    }

    public function testFilterWithNotProvidedRequiredParam()
    {
        $parameter = $this->getParameter('requiredParam');

        $result = $this->filter->filter(null, $parameter, false);
        $this->assertEquals('Value1', $result);
    }

    public function testFilterWithProvidedValueRequiredParam()
    {
        $parameter = $this->getParameter('requiredParam');

        $result = $this->filter->filter('AnotherVal', $parameter, true);
        $this->assertEquals('AnotherVal', $result);
    }

    public function testFilterWithProvidedNullOptionalParam()
    {
        $parameter = $this->getParameter('optionalParam');

        $result = $this->filter->filter(null, $parameter, true);
        $this->assertEquals(null, $result);
    }

    public function testFilterWithNotProvidedOptionalParam()
    {
        $parameter = $this->getParameter('optionalParam');

        $result = $this->filter->filter(null, $parameter, false);
        $this->assertEquals('Value2', $result);
    }

    public function testFilterWithProvidedValueOptionalParam()
    {
        $parameter = $this->getParameter('optionalParam');

        $result = $this->filter->filter('AnotherVal', $parameter, true);
        $this->assertEquals('AnotherVal', $result);
    }


    private function getParameter(string $name): DTOBuilderConstructorParameter
    {
        $method = new DTOBuilderConstructorMethod(new ReflectionMethod(TestClass3::class, '__construct'));
        return $method->getParameters()[$name];
    }

}

class TestClass3
{
    public function __construct(

        public string $requiredParam = 'Value1',

        public ?string $optionalParam = 'Value2',

    )
    {
    }


}
