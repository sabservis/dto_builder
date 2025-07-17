<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO\Builder;

use Psr\Container\ContainerInterface;
use SabServis\DTOBuilder\DTO\Builder\Filter\DTOArrayValueFilter;
use SabServis\DTOBuilder\DTO\Builder\Filter\DTOBooleanValueFilter;
use SabServis\DTOBuilder\DTO\Builder\Filter\DTODatetimeValueFilter;
use SabServis\DTOBuilder\DTO\Builder\Filter\DTODefaultValueFilter;
use SabServis\DTOBuilder\DTO\Builder\Filter\DTODtoValueFilter;
use SabServis\DTOBuilder\DTO\Builder\Filter\DTOEnumArrayValueFilter;
use SabServis\DTOBuilder\DTO\Builder\Filter\DTONumberValueFilter;
use SabServis\DTOBuilder\DTO\Builder\Filter\DTOStringValueFilter;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorMethod;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorParameter;
use SabServis\DTOBuilder\Exception\DTOCreationException;
use SabServis\DTOBuilder\Exception\DTOValidationException;
use SabServis\DTOBuilder\Helper\DIResolver;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DTOBuilder
{
    /**
     * @var list<class-string<\SabServis\DTOBuilder\DTO\Builder\Filter\DTOValueFilterInterface>>
     */
    protected array $valueFilters = [
        DTODefaultValueFilter::class,
        DTOArrayValueFilter::class,
        DTOEnumArrayValueFilter::class,
        DTOBooleanValueFilter::class,
        DTOStringValueFilter::class,
        DTODatetimeValueFilter::class,
        DTONumberValueFilter::class,
        DTODtoValueFilter::class,
    ];

    public function __construct(
        protected readonly ?ValidatorInterface $validator,
        protected readonly ContainerInterface $serviceLocator,
        protected readonly DIResolver $DIResolver,
    ) {
    }

    /**
     * @param array<mixed> $arguments
     * @param array<DTOBuilderConstructorParameter> $parameters
     * @throws \SabServis\DTOBuilder\Exception\DTOValidationException
     */
    protected function validateDTOConstruction(
        array $arguments,
        array $parameters,
    ): void {
        $list = new ConstraintViolationList();

        foreach ($parameters as $index => $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType();
            $isNullable = $type && $type->allowsNull();

            $argument = $arguments[$index];

            if (!is_null($argument)) {
                continue;
            }

            if ($isNullable) {
                continue;
            }

            $list->add(
                new ConstraintViolation(
                    message: 'Tato hodnota je povinná.',
                    messageTemplate: null,
                    parameters: [],
                    root: $name,
                    propertyPath: $name,
                    invalidValue: null,
                    code: 'badstate',
                ),
            );
        }

        if ($list->count()) {
            throw new DTOValidationException($list);
        }
    }

    protected function hydrateValue(
        mixed $value,
        DTOBuilderConstructorParameter $parameter,
        mixed $payload,
        bool $valueProvided,
    ): mixed {
        $fromFunctionHydrator = $parameter->getFromFunctionHydrator();

        try {
            if ($fromFunctionHydrator) {
                $value = $this->hydrateFromFunction($value, $fromFunctionHydrator, $parameter, $payload);
            } else {
                foreach ($this->valueFilters as $valueFilter) {
                    $filter = $this->serviceLocator->get($valueFilter);
                    $value = $filter->filter($value, $parameter, $valueProvided);
                }
            }
        } catch (DTOCreationException | DTOValidationException $exception) {
            $exception->payload = $payload;

            throw $exception;
        }

        return $value;
    }

    private function hydrateFromFunction(
        mixed $value,
        DTOBuilderConstructorMethod $fromFunctionHydrator,
        DTOBuilderConstructorParameter $parameter,
        mixed $payload,
    ): mixed {
        $reflectionParameters = $fromFunctionHydrator->getParameters();

        if ($parameter->isFromFunctionHydratorOnlyWithPayload()) {
            $resolvedParameters = [$payload];
        } else {
            $resolvedParameters = [$value];

            if ($fromFunctionHydrator->getReflectionParameterName()) {
                $resolvedParameters[] = $parameter;
                unset($reflectionParameters[$fromFunctionHydrator->getReflectionParameterName()]);
            }
        }

        array_shift($reflectionParameters);

        $di = $this->DIResolver->resolveParameterCallback($fromFunctionHydrator->getClassName());

        foreach ($reflectionParameters as $parameterDi) {
            $resolvedParameters[] = $di($parameterDi);
        }

        return $fromFunctionHydrator->getReflectionMethod()->invokeArgs(null, $resolvedParameters);
    }
}
