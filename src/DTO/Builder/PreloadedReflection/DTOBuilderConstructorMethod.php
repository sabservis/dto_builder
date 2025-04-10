<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO\Builder\PreloadedReflection;

class DTOBuilderConstructorMethod
{
    private string $name;
    private string $className;

    /** @var array<DTOBuilderConstructorParameter> */
    private array $parameters = [];

    private ?string $reflectionParameterName = null;

    public function __construct(
        private readonly \ReflectionMethod $reflectionMethod,
    )
    {
        foreach ($reflectionMethod->getParameters() as $parameter) {
            $param = new DTOBuilderConstructorParameter($parameter);
            $this->parameters[$parameter->getName()] = $param;

            if (
                $this->reflectionParameterName
                || $param->getType()?->getName() !== DTOBuilderConstructorParameter::class
            ) {
                continue;
            }

            $this->reflectionParameterName = $parameter->getName();
        }

        $this->name = $reflectionMethod->getName();
        $this->className = $reflectionMethod->getDeclaringClass()->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }

    /** @return array<DTOBuilderConstructorParameter> */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getReflectionMethod(): \ReflectionMethod
    {
        return $this->reflectionMethod;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getReflectionParameterName(): ?string
    {
        return $this->reflectionParameterName;
    }
}
