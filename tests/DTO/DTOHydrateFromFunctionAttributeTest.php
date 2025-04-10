<?php

namespace SabServis\DTOBuilder\Tests\DTO;

use SabServis\DTOBuilder\Attribute\HydrateFromFunction;
use SabServis\DTOBuilder\DTO\AbstractDTO;
use SabServis\DTOBuilder\DTO\Builder\DTOArrayBuilder;
use SabServis\DTOBuilder\Exception\DTOCreationException;

class DTOHydrateFromFunctionAttributeTest extends WithDIContainerTest
{

    private DTOArrayBuilder $builder;

    protected function setUp(): void {
        parent::setUp();
        $this->builder = $this->container->get(DTOArrayBuilder::class);
    }

    public function testHydrateFunction(): void {
        $data = [
            'value' => 1,
        ];
        $dto = $this->builder->build(DTO11::class, $data);

        $this->assertInstanceOf(DTO11::class, $dto);
        $this->assertEquals(6, $dto->value);
        $this->assertEquals((new \DateTime())->format('Y-m-d'), $dto->today);
    }

    public function testHydrateFunctionNotExists(): void {
        $data = [
            'value' => 1,
        ];
        $this->expectException(DTOCreationException::class);
        $this->builder->build(DTO22::class, $data);
    }

}

class DTO11 extends AbstractDTO
{
    public function __construct(

        #[HydrateFromFunction(functionName: [DTO11::class, 'hydrateData'])]
        public int $value,

        #[HydrateFromFunction(functionName: [DTO11::class, 'hydrateToday'])]
        public string $today,

    ) {
    }

    public static function hydrateData(mixed $value) {
        return $value + 5;
    }

    public static function hydrateToday(mixed $value, \DateTime $today) {
        return $today->format('Y-m-d');
    }
}

class DTO22 extends AbstractDTO
{
    public function __construct(

        #[HydrateFromFunction(functionName: [DTO22::class, 'hydrateData'])]
        public int $value,

    ) {
    }

}

