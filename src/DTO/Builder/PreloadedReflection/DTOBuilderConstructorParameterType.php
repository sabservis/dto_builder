<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO\Builder\PreloadedReflection;

use ReflectionType;

class DTOBuilderConstructorParameterType
{
    /** @var class-string|string */
    private ?string $name;

    /** @var array<DTOBuilderConstructorParameterType> in case of union type */
    private ?array $types = null;
    private bool $allowsNull;
    private bool $isBuiltin;

    public function __construct(
        private readonly ReflectionType $reflectionType,
    )
    {
        if ($reflectionType instanceof \ReflectionNamedType) {
            $this->name = $reflectionType->getName();
            $this->isBuiltin = $reflectionType->isBuiltin();
        } elseif (
            $reflectionType instanceof \ReflectionUnionType
            || $reflectionType instanceof \ReflectionIntersectionType
        ) {
            $this->name = null;
            $this->allowsNull = $reflectionType->allowsNull();
            $this->types = array_map(static fn (ReflectionType $type) => new DTOBuilderConstructorParameterType($type), $reflectionType->getTypes());
        }

        $this->allowsNull = $reflectionType->allowsNull();
    }

    /** @return class-string|string|null */
    public function getName(): ?string
    {
        return $this->name;
    }

    public function getReflectionType(): ReflectionType
    {
        return $this->reflectionType;
    }

    public function allowsNull(): bool
    {
        return $this->allowsNull;
    }

    public function isBuiltin(): bool
    {
        return $this->isBuiltin;
    }

    /** @return array<DTOBuilderConstructorParameterType>|null */
    public function getTypes(): ?array
    {
        return $this->types;
    }
}
