<?php

namespace SabServis\DTOBuilder\Tests\DTO;

use SabServis\DTOBuilder\Attribute\HydrateDateTime;
use SabServis\DTOBuilder\Attribute\HydrateToDateTime;
use SabServis\DTOBuilder\DTO\Builder\Filter\DTODatetimeValueFilter;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorMethod;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorParameter;
use SabServis\DTOBuilder\Enum\DateTimeFormatEnum;
use SabServis\DTOBuilder\Exception\DTOCreationException;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class DTODatetimeFilterTest extends TestCase
{

    private DTODatetimeValueFilter $filter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filter = new DTODatetimeValueFilter();
    }

    public function testFilterWithValidDateTime()
    {
        $parameter = $this->getParameter('dateTime');
        $dateTime = new \DateTime('2023-01-01 12:00:00');

        $result = $this->filter->filter($dateTime, $parameter, true);
        $this->assertEquals('2023-01-01T12:00:00+00:00', $result);
    }

    public function testFilterWithInvalidType()
    {
        $this->expectException(DTOCreationException::class);
        $parameter = $this->getParameter('dateTime');

        $this->filter->filter("not a datetime", $parameter, true);
    }


    public function testNotDatetimeAttribute()
    {
        $parameter = $this->getParameter('ignore');

        $result = $this->filter->filter("NotUpdated", $parameter, true);
        $this->assertEquals('NotUpdated', $result);
    }

    public function testFromDateTime()
    {
        $dateTime = new \DateTime('2023-01-01 12:00:00');

        $parameterDateTime = $this->getParameter('dateTime');
        $result = $this->filter->filter($dateTime, $parameterDateTime, true);
        $this->assertEquals($dateTime->format(DateTimeFormatEnum::DateTime->value), $result);

        $parameterDate = $this->getParameter('date');
        $result = $this->filter->filter($dateTime, $parameterDate, true);
        $this->assertEquals($dateTime->format(DateTimeFormatEnum::Date->value), $result);

        $parameterTime = $this->getParameter('time');
        $result = $this->filter->filter($dateTime, $parameterTime, true);
        $this->assertEquals($dateTime->format(DateTimeFormatEnum::Time->value), $result);
    }

    public function testToDatetime()
    {
        $dateTimeString = '2023-01-01T12:00:00+00:00';

        $parameterDateTime = $this->getParameter('stringDate');
        $result = $this->filter->filter($dateTimeString, $parameterDateTime, true);
        $this->assertEquals($dateTimeString, $result->format(DateTimeFormatEnum::DateTime->value));
        $this->assertInstanceOf(\DateTime::class, $result);

        $parameterDateTime = $this->getParameter('stringDateTime');
        $result = $this->filter->filter($dateTimeString, $parameterDateTime, true);
        $this->assertEquals($dateTimeString, $result->format(DateTimeFormatEnum::DateTime->value));
        $this->assertInstanceOf(\DateTimeImmutable::class, $result);
    }


    private function getParameter(string $name): DTOBuilderConstructorParameter
    {
        $method = new DTOBuilderConstructorMethod(new ReflectionMethod(TestClass2::class, '__construct'));
        return $method->getParameters()[$name];
    }

}

class TestClass2
{
    public function __construct(
        #[HydrateDateTime(format: DateTimeFormatEnum::DateTime)]
        public \DateTime $dateTime,

        #[HydrateDateTime(format: DateTimeFormatEnum::Date)]
        public \DateTime $date,

        #[HydrateDateTime(format: DateTimeFormatEnum::Time)]
        public \DateTime $time,

        #[HydrateToDateTime()]
        public string $stringDate,

        #[HydrateToDateTime(dateTimeClass: \DateTimeImmutable::class)]
        public string $stringDateTime,

        public \DateTime $ignore,

    ) {
    }


}
