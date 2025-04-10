<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO\Builder\Filter;

use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorParameter;
use SabServis\DTOBuilder\Exception\DTOCreationException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class DTONumberValueFilter implements DTOValueFilterInterface
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

        if ($parameterType === 'int' || $parameterType === 'float') {
            if (!is_numeric($value)) {
                $list = new ConstraintViolationList();
                $list->add(
                    new ConstraintViolation(
                        message: 'Tato hodnota musí být číslo.',
                        messageTemplate: null,
                        parameters: [],
                        root: $value,
                        propertyPath: $parameter->getName(),
                        invalidValue: $value,
                        code: 'notanumber',
                    ),
                );

                throw new DTOCreationException($list);
            }

            return $parameterType === 'int' ? (int)$value : (float)$value;
        }

        return $value;
    }
}
