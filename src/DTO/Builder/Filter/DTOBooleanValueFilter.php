<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO\Builder\Filter;

use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorParameter;
use SabServis\DTOBuilder\Exception\DTOCreationException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class DTOBooleanValueFilter implements DTOValueFilterInterface
{
    public function filter(
        mixed $value,
        DTOBuilderConstructorParameter $parameter,
        bool $valueProvided,
    ): mixed {
        if (is_null($value)) {
            return null;
        }

        $parameterType = $parameter->getType()?->getName();

        if ($parameterType === 'bool') {
            if (!in_array($value, [1, 0, 'true', 'false', true, false], true)) {
                $list = new ConstraintViolationList();
                $list->add(
                    new ConstraintViolation(
                        message: 'Tato hodnota musí být pravdivostní.',
                        messageTemplate: null,
                        parameters: [],
                        root: $value,
                        propertyPath: $parameter->getName(),
                        invalidValue: $value,
                        code: 'notabool',
                    ),
                );

                throw new DTOCreationException($list);
            }

            return match ($value) {
                1, true, 'true' => true,
                default => false
            };
        }

        return $value;
    }
}
