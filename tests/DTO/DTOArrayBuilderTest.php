<?php

namespace SabServis\DTOBuilder\Tests\DTO;

use SabServis\DTOBuilder\Attribute\HydrateColumn;
use SabServis\DTOBuilder\Attribute\HydrateFromFunction;
use SabServis\DTOBuilder\DTO\AbstractDTO;
use SabServis\DTOBuilder\DTO\Builder\DTOArrayBuilder;
use SabServis\DTOBuilder\Exception\DTOCreationException;
use SabServis\DTOBuilder\Exception\DTOValidationException;
use SabServis\DTOBuilder\Helper\HydrateEnum;
use SabServis\DTOBuilder\Tests\DTO\Utils\GenderEnum;

class DTOArrayBuilderTest extends WithDIContainerTest
{

    private DTOArrayBuilder $builder;

    protected function setUp(): void {
        parent::setUp();
        $this->builder = $this->container->get(DTOArrayBuilder::class);
    }

    public function testBuildWithValidData() {
        $data = ['name' => 'John', 'age' => 30, 'gender' => 'M'];
        $dto = $this->builder->build(SampleDTO1::class, $data);

        $this->assertInstanceOf(SampleDTO1::class, $dto);
        $this->assertEquals('John', $dto->name);
        $this->assertEquals(30, $dto->age);
        $this->assertEquals(GenderEnum::Male, $dto->gender);
    }

    public function testBuildWithMissingData() {
        $this->expectException(DTOValidationException::class);
        $data = ['name' => 'John', 'gender' => 'Male', 'gender' => 'M']; // Missing 'age'
        $dto = $this->builder->build(SampleDTO1::class, $data);
    }

    public function testBuildWithInvalidDataType() {
        $this->expectException(DTOCreationException::class);
        $data = ['name' => 'John', 'age' => 'thirty', 'gender' => 'M']; // Invalid type for 'age'
        $dto = $this->builder->build(SampleDTO1::class, $data);
    }

    public function testBuildWithExtraData() {
        $data = ['name' => 'John', 'age' => 30, 'extra' => 'data', 'gender' => 'M'];
        $dto = $this->builder->build(SampleDTO1::class, $data);

        $this->assertInstanceOf(SampleDTO1::class, $dto);
        $this->assertEquals('John', $dto->name);
        $this->assertEquals(30, $dto->age);
        // Assume we ignore extra data not defined in DTO
    }

    public function testBuildWithDotAccess() {
        $data = ['object' => ['name' => 'John']];
        $dto = $this->builder->build(SampleDTO4::class, $data);

        $this->assertInstanceOf(SampleDTO4::class, $dto);
        $this->assertEquals('John', $dto->name);
        $this->assertEquals(null, $dto->age);
    }

}

class SampleDTO1 extends AbstractDTO
{
    public function __construct(
        public string $name,
        public int $age,
        #[HydrateFromFunction(functionName: [HydrateEnum::class, 'hydrateEnum'])]
        public GenderEnum $gender,
    ) {
    }
}

class SampleDTO4 extends AbstractDTO
{
    public function __construct(
        #[HydrateColumn(name: 'object.name')]
        public string $name,

        #[HydrateColumn(name: 'object.age')]
        public ?int $age
    ) {
    }
}
