<?php

namespace SabServis\DTOBuilder\Tests\DTO;

use PHPUnit\Framework\TestCase;
use SabServis\DTOBuilder\Attribute\HydrateColumn;
use SabServis\DTOBuilder\DTO\AbstractDTO;
use SabServis\DTOBuilder\DTO\Builder\DTOEntityBuilder;
use SabServis\DTOBuilder\Exception\DTOCreationException;
use SabServis\DTOBuilder\Exception\DTOValidationException;

class DTOEntityBuilderTest extends WithDIContainerTest
{

    private DTOEntityBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = $this->container->get(DTOEntityBuilder::class);
    }

    public function testBuildWithValidData()
    {
        $entity = new SampleEntity();
        $entity->name = 'John';
        $entity->age = 30;

        $dto = $this->builder->build(SampleDTO2::class, $entity);

        $this->assertInstanceOf(SampleDTO2::class, $dto);
        $this->assertEquals($entity->name, $dto->name);
        $this->assertEquals($entity->age, $dto->age);
        $this->assertEquals($entity->getNameWithAge(), $dto->nameWithAge);
        $this->assertEquals($entity->getNameWithAge(), $dto->name_with_age);
        $this->assertEquals($entity->getNameWithAgeFormatted(), $dto->nameWithAgeFormatted);
        $this->assertEquals($entity->getFullName(), $dto->fullname);
    }

    public function testBuildWithMissingData()
    {
        $this->expectException(DTOValidationException::class);

        $entity = new SampleMissingEntity();
        $entity->name = 'John';

        $dto = $this->builder->build(SampleDTO2::class, $entity);
    }

    public function testBuildWithInvalidDataType()
    {
        $this->expectException(DTOCreationException::class);
        $entity = new SampleInvalidEntity();
        $entity->name = 'John';
        $entity->age = 'thirty';
        $this->builder->build(SampleDTO2::class, $entity);
    }

    public function testBuildWithExtraData()
    {
        $entity = new SampleExtraEntity();
        $entity->id = 1;
        $entity->name = 'John';
        $entity->age = 30;

        $dto = $this->builder->build(SampleDTO2::class, $entity);

        $this->assertInstanceOf(SampleDTO2::class, $dto);
        $this->assertEquals($entity->name, $dto->name);
        $this->assertEquals($entity->age, $dto->age);
        // Assume we ignore extra data not defined in DTO
    }

    public function testBuildWithDotAccess()
    {
        $entity = new SampleDotAccessEntity();
        $entity->object = new SampleMissingEntity();
        $entity->object->name = 'John';
        $dto = $this->builder->build(SampleDTO5::class, $entity);

        $this->assertInstanceOf(SampleDTO5::class, $dto);
        $this->assertEquals('John', $dto->name);
        $this->assertEquals(null, $dto->birthday);
    }

}

class SampleEntity
{
    public string $name;
    public int $age;

    public function getNameWithAge(): string
    {
        return $this->name . $this->age;
    }

    public function getFullName(?string $prefix = null): string
    {
        return ($prefix ?: '') . $this->name;
    }

    public function getNameWithAgeFormatted(string $format = 'normal'): string
    {
        return $this->age . $this->name;
    }
}

class SampleMissingEntity
{
    public string $name;
}


class SampleInvalidEntity
{
    public string $name;
    public string $age;
}

class SampleExtraEntity
{
    public int $id;
    public string $name;
    public int $age;

    public function getNameWithAge(): string
    {
        return $this->name . $this->age;
    }

    public function getFullName(?string $prefix = null): string
    {
        return ($prefix ?: '') . $this->name;
    }

    public function getNameWithAgeFormatted(string $format = 'normal'): string
    {
        return $this->age . $this->name;
    }
}

class SampleDotAccessEntity
{
    public SampleMissingEntity $object;
}

class SampleDTO2 extends AbstractDTO
{
    public function __construct(
        public string $name,
        public int $age,
        public string $nameWithAge,
        public string $name_with_age,
        public string $nameWithAgeFormatted,
        public string $fullname,
    ) {
    }
}

class SampleDTO5 extends AbstractDTO
{
    public function __construct(
        #[HydrateColumn(name: 'object.name')]
        public string $name,

        #[HydrateColumn(name: 'object.birthday')]
        public ?\DateTime $birthday
    ) {
    }
}
