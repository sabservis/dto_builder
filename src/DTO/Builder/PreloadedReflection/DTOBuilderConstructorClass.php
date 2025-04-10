<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO\Builder\PreloadedReflection;

use ReflectionClass;
use SabServis\DTOBuilder\DTO\AbstractDTO;

/**
 * @template T of AbstractDTO
 */
class DTOBuilderConstructorClass
{
    /** @var ReflectionClass<T> */
    private ReflectionClass $reflectionClass;

    /** @var array<DTOBuilderConstructorParameter> */
    private array $parameters = [];

    public function __construct(
        /** @var class-string<T> */
        private readonly string $className,
    )
    {
        $this->reflectionClass = new ReflectionClass($className);
        $this->initConstructorParameters($className);
    }

    /** @return array<DTOBuilderConstructorParameter> */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /** @return ReflectionClass<T> */
    public function getReflectionClass(): ReflectionClass
    {
        return $this->reflectionClass;
    }

    /** @return class-string<T> */
    public function getClassName(): string
    {
        return $this->className;
    }

    /** @param class-string<T> $className */
    protected function initConstructorParameters(string $className): void
    {
        $reflectionClass = new ReflectionClass($className);
        $constructor = $reflectionClass->getConstructor();

        $parameters = $constructor ? $constructor->getParameters() : [];

        foreach ($parameters as $parameter) {
            $this->parameters[] = new DTOBuilderConstructorParameter($parameter);
        }
    }
}
