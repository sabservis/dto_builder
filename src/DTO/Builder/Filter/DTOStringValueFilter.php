<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO\Builder\Filter;

use SabServis\DTOBuilder\Attribute\HydrateString;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorParameter;
use SabServis\DTOBuilder\Exception\DTOCreationException;
use SabServis\DTOBuilder\Helper\StringsHelper;
use Stringable;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class DTOStringValueFilter implements DTOValueFilterInterface
{
    public function filter(
        mixed $value,
        DTOBuilderConstructorParameter $parameter,
        bool $valueProvided,
    ): mixed {
        if (is_null($value)) {
            return null;
        }

        $stringHydrator = $parameter->getAttribute(HydrateString::class);

        $parameterType = $parameter->getType()?->getName();

        if (!$stringHydrator) {
            return $value;
        }

        if ($parameterType === 'string') {
            if (
                $stringHydrator->getArgument('emptyToNull') === true &&
                is_string($value) &&
                StringsHelper::emptyToNull($value) === null
            ) {
                return null;
            }

            $this->validateStringable($value, $parameter);

            return (string)$value;
        }

        $list = new ConstraintViolationList();
        $list->add($this->createNotStringViolation($value, $parameter));

        throw new DTOCreationException($list);
    }

    /**
     * @throws DTOCreationException
     */
    private function validateStringable(
        mixed $value,
        DTOBuilderConstructorParameter $parameter,
    ): void
    {
        if (!(is_scalar($value) || (is_object($value) && $value instanceof Stringable))) {
            $list = new ConstraintViolationList();
            $list->add($this->createNotStringableViolation($value, $parameter));

            throw new DTOCreationException($list);
        }
    }

    private function createNotStringViolation(
        mixed $value,
        DTOBuilderConstructorParameter $parameter,
    ): ConstraintViolation
    {
        return new ConstraintViolation(
            message: 'HydrateString může být aplikován pouze na string property.',
            messageTemplate: null,
            parameters: [],
            root: $value,
            propertyPath: $parameter->getName(),
            invalidValue: $value,
            code: 'notastring',
        );
    }

    private function createNotStringableViolation(
        mixed $value,
        DTOBuilderConstructorParameter $parameter,
    ): ConstraintViolation
    {
        return new ConstraintViolation(
            message: 'Tato hodnota musí být převoditelná na text.',
            messageTemplate: null,
            parameters: [],
            root: $value,
            propertyPath: $parameter->getName(),
            invalidValue: $value,
            code: 'notastring',
        );
    }
}
