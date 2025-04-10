<?php

namespace SabServis\DTOBuilder\Tests\DTO;

use SabServis\DTOBuilder\Attribute\HydrateColumn;
use SabServis\DTOBuilder\DTO\AbstractDTO;
use SabServis\DTOBuilder\DTO\Builder\DTOArrayBuilder;
use SabServis\DTOBuilder\Exception\DTOValidationException;

class DTOHydrateColumnAttributeTest extends WithDIContainerTest
{

    private DTOArrayBuilder $builder;

    protected function setUp(): void {
        parent::setUp();
        $this->builder = $this->container->get(DTOArrayBuilder::class);
    }

    public function testHydrateColumn(): void {
        $data = [
            'nazev' => 'John',
        ];
        $dto = $this->builder->build(DTO1::class, $data);

        $this->assertInstanceOf(DTO1::class, $dto);
        $this->assertEquals($data['nazev'], $dto->name);
    }

    public function testHydrateRelationColumn(): void {
        $data = [
            'nazev' => 'John',
            'relace' => [
                'id' => 1,
                'TNazev' => 'Relacni jmeno'
            ],
        ];
        $dto = $this->builder->build(DTO2::class, $data);

        $this->assertInstanceOf(DTO2::class, $dto);
        $this->assertEquals($data['nazev'], $dto->name);
        $this->assertEquals($data['relace']['TNazev'], $dto->targetDTO->targetName);
        $this->assertEquals($data['relace']['id'], $dto->targetDTO->id);
    }

    public function testHydrateColumnNameIsSameAsInDTO(): void {
        $data = [
            'name' => 'John',
        ];
        $this->expectException(DTOValidationException::class);
        $this->builder->build(DTO1::class, $data);
    }

    public function testHydrateColumnNameNotExists(): void {
        $data = [
            'asadsfadsf' => 'John',
        ];
        $this->expectException(DTOValidationException::class);
        $this->builder->build(DTO1::class, $data);
    }

    public function testHydrateColumnNull(): void {
        $data = [
            'nazev' => null,
        ];
        $dto = $this->builder->build(DTO3::class, $data);

        $this->assertInstanceOf(DTO3::class, $dto);
        $this->assertEquals($data['nazev'], $dto->nullableName);
    }

    public function testHydrateComplexDataWithArrayTarget(): void {
        $data = [
            'nazev' => 'John',
            'cislo' => 30,
            'targetDTO' => ['id' => 1, 'TNazev' => 'Jan'],
            'pole' => [['id' => 2, 'TNazev' => 'Doe']]
        ];
        $dto = $this->builder->build(SampleDTO3::class, $data);

        $this->assertInstanceOf(SampleDTO3::class, $dto);
        $this->assertEquals($data['nazev'], $dto->name);
        $this->assertEquals($data['cislo'], $dto->age);
        $this->assertEquals(1, count($dto->array));
        $this->assertEquals(TargetDTO::class, $dto->targetDTO::class);
        $this->assertEquals($data['pole'][0]['TNazev'], $dto->array[0]->targetName);
    }

}

class DTO1 extends AbstractDTO
{
    public function __construct(

        #[HydrateColumn(name: 'nazev')]
        public string $name,

    ) {
    }
}

class DTO2 extends AbstractDTO
{
    public function __construct(

        #[HydrateColumn(name: 'nazev')]
        public string $name,

        #[HydrateColumn(name: 'relace')]
        public TargetDTO $targetDTO,
    ) {
    }
}

class DTO3 extends AbstractDTO
{
    public function __construct(

        #[HydrateColumn(name: 'nazev')]
        public ?string $nullableName,

    ) {
    }
}

class SampleDTO3 extends AbstractDTO
{
    public function __construct(

        #[HydrateColumn(name: 'nazev')]
        public string $name,

        #[HydrateColumn(name: 'cislo')]
        public int $age,

        #[HydrateColumn(name: 'targetDTO')]
        public TargetDTO $targetDTO,

        #[HydrateColumn(name: 'pole', arrayTarget: TargetDTO::class)]
        public array $array,
    ) {
    }
}

class TargetDTO extends AbstractDTO
{
    public function __construct(
        public int $id,

        #[HydrateColumn(name: 'TNazev')]
        public string $targetName,

    ) {
    }
}
