<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO\Builder\PreloadedReflection;

use ReflectionProperty;

class DTOEntityBuilderSourceProperty
{
    private bool $isPublic;
    private string $name;

    public function __construct(
        private ReflectionProperty $reflectionProperty,
    )
    {
        $this->isPublic = $this->reflectionProperty->isPublic();
        $this->name = $this->reflectionProperty->getName();
    }

    public function getReflectionProperty(): ReflectionProperty
    {
        return $this->reflectionProperty;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(mixed $object): mixed
    {
        return $this->isPublic ? $object->{$this->name} : $this->reflectionProperty->getValue($object);
    }
}
