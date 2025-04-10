<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO\Builder\PreloadedReflection;

use ReflectionParameter;
use SabServis\DTOBuilder\Attribute\HydrateColumn;
use SabServis\DTOBuilder\Attribute\HydrateFromFunction;
use SabServis\DTOBuilder\Exception\DTOCreationException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class DTOBuilderConstructorParameter
{
    private string $name;
    private ?DTOBuilderConstructorParameterType $type;
    private ?DTOBuilderConstructorMethod $fromFunctionHydrator;

    /** @var array<DTOBuilderConstructorParameterAttribute<object>> */
    private array $attributes = [];
    private mixed $defaultValue;
    private bool $allowsNull;
    private bool $isDefaultValueAvailable;
    private bool $fromFunctionHydratorOnlyWithPayload = false;
    private bool $hasType;
    private bool $isOptional;

    public function __construct(
        private readonly ReflectionParameter $reflectionParameter,
    )
    {
        foreach ($reflectionParameter->getAttributes() as $attribute) {
            $this->attributes[$attribute->getName()] = new DTOBuilderConstructorParameterAttribute($attribute);
        }

        $this->name = $this->getParameterName($reflectionParameter);
        $this->type = $this->reflectionParameter->getType() ? new DTOBuilderConstructorParameterType($this->reflectionParameter->getType()) : null;
        $this->hasType = $this->reflectionParameter->hasType();
        $this->isOptional = $this->reflectionParameter->isOptional();
        $this->isDefaultValueAvailable = $this->reflectionParameter->isDefaultValueAvailable();
        $this->allowsNull = $this->reflectionParameter->allowsNull();
        $this->defaultValue = $this->isDefaultValueAvailable ? $this->reflectionParameter->getDefaultValue() : null;
        $this->initFromFunctionHydrator();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isOptional(): bool
    {
        return $this->isOptional;
    }

    /** @return array<DTOBuilderConstructorParameterAttribute<object>> */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /** @return DTOBuilderConstructorParameterAttribute<object> */
    public function getAttribute(string $name): ?DTOBuilderConstructorParameterAttribute
    {
        return $this->attributes[$name] ?? null;
    }

    public function getFromFunctionHydrator(): ?DTOBuilderConstructorMethod
    {
        return $this->fromFunctionHydrator;
    }

    public function isFromFunctionHydratorOnlyWithPayload(): bool
    {
        return $this->fromFunctionHydratorOnlyWithPayload;
    }

    public function getReflectionParameter(): ReflectionParameter
    {
        return $this->reflectionParameter;
    }

    public function getType(): ?DTOBuilderConstructorParameterType
    {
        return $this->type;
    }

    public function isDefaultValueAvailable(): bool
    {
        return $this->isDefaultValueAvailable;
    }

    public function allowsNull(): bool
    {
        return $this->allowsNull;
    }

    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    public function hasType(): bool
    {
        return $this->hasType;
    }

    protected function getParameterName(ReflectionParameter $parameter): string
    {
        $paramName = $parameter->getName();

        if (isset($this->attributes[HydrateColumn::class])) {
            $paramName = $this->attributes[HydrateColumn::class]->getArgument('name');
        }

        return $paramName;
    }

    private function initFromFunctionHydrator(): void
    {
        $hydrator = $this->getAttribute(HydrateFromFunction::class);

        if (!$hydrator) {
            $this->fromFunctionHydrator = null;

            return;
        }

        $function = $hydrator->getArgument('functionName');
        \assert(\is_array($function));
        [$functionClass, $functionMethod] = $function;

        if (!method_exists($functionClass, $functionMethod)) {
            $list = new ConstraintViolationList();
            $list->add(
                new ConstraintViolation(
                    message: 'Neexistuje funkce ' . $functionClass . '::' . $functionMethod,
                    messageTemplate: null,
                    parameters: [],
                    root: null,
                    propertyPath: '',
                    invalidValue: null,
                    code: 'notexistfunction',
                ),
            );

            throw new DTOCreationException($list);
        }

        $reflMethod = new DTOBuilderConstructorMethod(new \ReflectionMethod($functionClass, $functionMethod));

        $this->fromFunctionHydrator = $reflMethod;

        $this->fromFunctionHydratorOnlyWithPayload = $hydrator->getArgument('callOnlyWithPayload') ?: false;
    }
}
