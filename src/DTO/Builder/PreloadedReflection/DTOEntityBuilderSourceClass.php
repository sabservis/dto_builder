<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO\Builder\PreloadedReflection;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

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

    /** @var array<?ReflectionMethod> */
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

    public function getGetter(
        string $paramName,
    ): ?ReflectionMethod {
        $getterName = strtolower($this->paramNameToGetterName($paramName));

        if (array_key_exists($getterName, $this->getGetterCache)) {
            return $this->getGetterCache[$getterName];
        }

        if (isset($this->methods[$getterName])) {
            $method = $this->methods[$getterName];
            $hasRequiredParameter = false;

            foreach ($method->getParameters() as $parameter) {
                if ($parameter->isOptional() || $parameter->isDefaultValueAvailable()) {
                    continue;
                }

                $hasRequiredParameter = true;
            }

            if (!$hasRequiredParameter) {
                $this->getGetterCache[$getterName] = $method;

                return $method;
            }
        }

        $this->getGetterCache[$getterName] = null;

        return null;
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
