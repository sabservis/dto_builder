<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO\Builder\PreloadedReflection;

use ReflectionAttribute;

/**
 * @template T of object
 */
class DTOBuilderConstructorParameterAttribute
{
    private string $name;

    /** @var array<mixed> */
    private array $arguments = [];

    /**
     * @param ReflectionAttribute<T> $reflectionAttribute
     */
    public function __construct(
        private readonly ReflectionAttribute $reflectionAttribute,
    )
    {
        $this->name = $reflectionAttribute->getName();

        foreach ($reflectionAttribute->getArguments() as $name => $argument) {
            $this->arguments[$name] = $argument;
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    /** @return array<string, ReflectionAttribute<T>> */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getArgument(string $name): mixed
    {
        return $this->arguments[$name] ?? null;
    }

    /**
     * @return ReflectionAttribute<T>
     */
    public function getReflectionAttribute(): ReflectionAttribute
    {
        return $this->reflectionAttribute;
    }
}
