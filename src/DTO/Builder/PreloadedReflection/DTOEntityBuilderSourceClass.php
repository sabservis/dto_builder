<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO\Builder\PreloadedReflection;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use SabServis\DTOBuilder\Helper\DIResolver;

/**
 * @template T of object
 */
class DTOEntityBuilderSourceClass
{
    /** @var ReflectionClass<T> */
    private ReflectionClass $reflectionClass;

    /** @var array<ReflectionMethod> */
    private array $methods = [];

    /** @var array<DTOEntityBuilderSourceProperty> */
    private array $properties = [];

    /** @var array<array{ReflectionMethod|null, array<object>|null}> */
    private array $getGetterCache = [];

    public function __construct(
        /** @var class-string<T> */
        private string $className,
    )
    {
        $this->reflectionClass = new ReflectionClass($className);

        foreach ($this->reflectionClass->getMethods() as $method) {
            $this->methods[strtolower($method->getName())] = $method;
        }

        foreach ($this->reflectionClass->getProperties() as $property) {
            $this->properties[$property->getName()] = new DTOEntityBuilderSourceProperty($property);
        }
    }

    /** @return ReflectionClass<T> */
    public function getReflectionClass(): ReflectionClass
    {
        return $this->reflectionClass;
    }

    /**
     * @return array{ReflectionMethod|null, array<object>|null}
     */
    public function getGetter(
        string $paramName,
        DIResolver $diResolver,
    ): array {
        $getterName = strtolower($this->paramNameToGetterName($paramName));

        if (array_key_exists($getterName, $this->getGetterCache)) {
            return $this->getGetterCache[$getterName];
        }

        if (isset($this->methods[$getterName])) {
            $args = [];
            $method = $this->methods[$getterName];

            foreach ($method->getParameters() as $parameter) {
                if ($parameter->isOptional() || $parameter->isDefaultValueAvailable()) {
                    continue;
                }

                $di = $diResolver->resolveParameterCallback($this->className);
                $reflectionParameters = $method->getParameters();

                foreach ($reflectionParameters as $parameterDi) {
                    $args[] = $di($parameterDi);
                }
            }

            $this->getGetterCache[$getterName] = [$method, $args];

            return [$method, $args];
        }

        $this->getGetterCache[$getterName] = [null, null];

        return [null, null];
    }

    public function getProperty(string $paramName): ?DTOEntityBuilderSourceProperty
    {
        return $this->properties[$paramName] ?? null;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    private function paramNameToGetterName(string $paramName): string
    {
        $camelCaseName = (strpos($paramName, '_') !== false) ? str_replace('_', '', ucwords($paramName, '_')) : ucfirst(
            $paramName,
        );

        return 'get' . $camelCaseName;
    }
}
