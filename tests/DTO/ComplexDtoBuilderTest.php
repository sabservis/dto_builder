<?php

namespace SabServis\DTOBuilder\Tests\DTO;

use SabServis\DTOBuilder\DTO\Builder\DTOArrayBuilder;
use SabServis\DTOBuilder\DTO\Builder\DTOEntityBuilder;
use SabServis\DTOBuilder\DTO\Builder\DTOMultiArrayBuilder;
use SabServis\DTOBuilder\DTO\Builder\DTOMultiEntityBuilder;
use SabServis\DTOBuilder\Tests\DTO\Utils\GenderEnum;

class ComplexDtoBuilderTest extends WithDIContainerTest
{

    private DTOArrayBuilder $dtoArrayBuilder;
    private DTOEntityBuilder $dtoEntityBuilder;
    private DTOMultiArrayBuilder $dtoMultiArrayBuilder;
    private DTOMultiEntityBuilder $dtoMultiEntityBuilder;

    protected function setUp(): void {
        parent::setUp();
        $this->dtoArrayBuilder = $this->container->get(DTOArrayBuilder::class);
        $this->dtoEntityBuilder = $this->container->get(DTOEntityBuilder::class);
        $this->dtoMultiArrayBuilder = $this->container->get(DTOMultiArrayBuilder::class);
        $this->dtoMultiEntityBuilder = $this->container->get(DTOMultiEntityBuilder::class);
    }

    public function testBuildWithArrayData() {
        $data = $this->getInputArrayData();
        $outer = $this->dtoArrayBuilder->build(Utils\OuterDTO::class, $data);

        $this->assertOuter($outer);
    }

    public function testBuildWithObjectData() {
        $data = $this->getInputObjectData();

        $outer = $this->dtoEntityBuilder->build(Utils\OuterDTO::class, $data);

        $this->assertOuter($outer);
    }

    public function testBuildWithAMultirrayData() {
        $data = [$this->getInputArrayData(), $this->getInputArrayData()];
        $list = $this->dtoMultiArrayBuilder->build(Utils\OuterDTO::class, $data);

        foreach ($list as $item) {
            $this->assertOuter($item);
        }
        $this->assertCount(2, $list);
    }

    public function testBuildWithMultiObjectData() {
        $data = [$this->getInputObjectData(), $this->getInputObjectData()];

        $list = $this->dtoMultiEntityBuilder->build(Utils\OuterDTO::class, $data);

        foreach ($list as $item) {
            $this->assertOuter($item);
        }

        $this->assertCount(2, $list);
    }

    public function testBuildWithMultiArrayDataPerformance() {
        $count = 10000;
        $data = array_map(
            fn() => $this->getInputArrayData(),
            array_fill(0, $count, null)
        );

        $list = $this->dtoMultiArrayBuilder->build(Utils\OuterDTO::class, $data);

        $this->assertCount($count, $list);

        foreach ($list as $item) {
            $this->assertOuter($item);
        }
    }

    public function testBuildWithMultiObjectDataPerformance() {
        $count = 10000;
        $data = array_map(
            fn() => $this->getInputObjectData(),
            array_fill(0, $count, null)
        );

        $list = $this->dtoMultiEntityBuilder->build(Utils\OuterDTO::class, $data);

        $this->assertCount($count, $list);

        foreach ($list as $item) {
            $this->assertOuter($item);
        }
    }

    public function assertOuter(Utils\OuterDTO $outer) {
        $this->assertInstanceOf(Utils\OuterDTO::class, $outer);
        $this->assertEquals('John', $outer->name);
        $this->assertEquals(30, $outer->age);
        $this->assertEquals(GenderEnum::Female, $outer->gender);
        $this->assertEquals('2020-01-01T15:15:15', $outer->dateTime);
        $this->assertEquals('2020-01-02', $outer->date);
        $this->assertEquals('15:15:15', $outer->time);
        $this->assertEquals(new \DateTimeImmutable('01-01-2020T15:15:15'), $outer->stringDateTime);
        $this->assertEquals(new \DateTime('02-01-2020T15:15:15'), $outer->stringDate);
        $this->assertEquals('Value1', $outer->requiredParamWithDefault);
        $this->assertEquals(null, $outer->optionalParamWithDefault);
        $this->assertEquals(10, $outer->valueFromFunction);
        $this->assertEquals((new \DateTime())->format('Y-m-d'), $outer->todayFromFunction);
        $this->assertEquals('15', $outer->stringFromInt);
        $this->assertEquals('15.15', $outer->stringFromFloat);
        $this->assertEquals('stringable', $outer->stringFromStringable);

        foreach ($outer->innerDTOs as $innerDTO) {
            $this->assertInstanceOf(Utils\InnerDTO::class, $innerDTO);
            $this->assertStringStartsWith('John', $innerDTO->name);
            $this->assertGreaterThan(16, $innerDTO->value);
        }

        $this->assertInner($outer->innerDTO);
    }

    public function assertInner(Utils\InnerDTO $inner) {
        $this->assertInstanceOf(Utils\InnerDTO::class, $inner);
        $this->assertEquals('John', $inner->name);
        $this->assertEquals(null, $inner->age);
        $this->assertEquals(35, $inner->value);
    }


    private function getInputObjectData(): object {
        $data = (object)$this->getInputArrayData();
        $data->innerObject = (object)$data->innerObject;
        $data->innerObject->object = (object)$data->innerObject->object;
        $data->items = array_map(fn($innerObject) => (object)$innerObject, $data->items);
        array_walk($data->items, function ($innerObject) {
            $innerObject->object = (object)$innerObject->object;
        });

        return $data;
    }

    private function getInputArrayData(): array {
        $stringable = \Mockery::mock(\Stringable::class);
        $stringable->shouldReceive('__toString')->andReturn('stringable');

        return
            [
                'name' => 'John',
                'age' => 30,
                'extra' => 'data',
                'gender' => 'F',
                'innerObject' => ['object' => [
                    'name' => 'John',
                    'age' => null,
                    'value' => 30,
                ]
                ],
                'items' => [
                    ['object' => [
                        'name' => 'John',
                        'age' => null,
                        'value' => 30,
                    ]],
                    ['object' => [
                        'name' => 'John2',
                        'age' => 12,
                        'value' => 32,
                    ]],
                    ['object' => [
                        'name' => 'John3',
                        'age' => null,
                        'value' => 12,
                    ]],
                    ['object' => [
                        'name' => 'John4',
                        'age' => 23,
                        'value' => 123,
                    ]],
                ],
                'dateTime' => new \DateTime('01-01-2020T15:15:15'),
                'date' => new \DateTime('02-01-2020T15:15:15'),
                'time' => new \DateTime('03-01-2020T15:15:15'),
                'stringDateTime' => '01-01-2020T15:15:15',
                'stringDate' => '02-01-2020T15:15:15',
                'requiredParamWithDefault' => null,
                'optionalParamWithDefault' => null,
                'valueFromFunction' => 5,
                'todayFromFunction' => null,
                'stringFromInt' => 15,
                'stringFromFloat' => 15.15,
                'stringFromStringable' => $stringable,
            ];
    }
}
