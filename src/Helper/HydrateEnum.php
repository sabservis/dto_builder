<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Helper;

use BackedEnum;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorParameter;
use SabServis\DTOBuilder\Exception\DTOValidationException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use UnitEnum;

class HydrateEnum
{
    /**
     * Hydrates enum by value field
     */
    public static function hydrateEnum(
        mixed $value,
        DTOBuilderConstructorParameter $c,
    ): mixed {
        $enumClass = $c->getType()?->getName();
        $paramName = $c->getName();

        if ($enumClass && $paramName) {
            if (((is_null($value)) || $value === '') && $c->allowsNull()) {
                return null;
            }

            try {
                $result = $enumClass::tryFrom($value);
            } catch (\Throwable) {
                $result = null;
            }

            if ($result !== null) {
                return $result;
            }
        }

        $list = new ConstraintViolationList();
        $list->add(
            new ConstraintViolation(
                message: isset($value) ? 'Tato hodnota nená platná.' : 'Tato hodnota je povinná.',
                messageTemplate: null,
                parameters: [],
                root: $paramName,
                propertyPath: $paramName,
                invalidValue: $value,
                code: 'badstate',
            ),
        );

        throw new DTOValidationException($list);
    }

    /**
     * Hydrates enum by name field.
     *
     */
    public static function hydrateEnumByName(
        mixed $value,
        DTOBuilderConstructorParameter $c,
    ): mixed {
        /** @var class-string $enumClass */
        $enumClass = $c->getType()?->getName();
        $paramName = $c->getName();

        foreach ($enumClass::cases() as $case) {
            if (isset($value) && $case->name === $value) {
                return $case;
            }
        }

        if (((is_null($value)) || $value === '') && $c->allowsNull()) {
            return null;
        }

        $list = new ConstraintViolationList();
        $message = isset($value) ? '"' . $value . '" není platná hodnota pro atribut "%s".' : 'Tato hodnota je povinná pro atribut "%s".';
        $list->add(
            new ConstraintViolation(
                message: sprintf($message, $c->getName()),
                messageTemplate: null,
                parameters: [],
                root: $paramName,
                propertyPath: $paramName,
                invalidValue: $value ?? null,
                code: 'badstate',
            ),
        );

        throw new DTOValidationException($list);
    }

    /**
     * Hydrates enum to value.
     *
     */
    public static function hydrateToValue(BackedEnum $value): mixed {
        return $value->value;
    }
}
