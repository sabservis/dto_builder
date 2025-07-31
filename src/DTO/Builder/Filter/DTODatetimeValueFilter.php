<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO\Builder\Filter;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use SabServis\DTOBuilder\Attribute\HydrateDateTime;
use SabServis\DTOBuilder\Attribute\HydrateToDateTime;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorParameter;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorParameterAttribute;
use SabServis\DTOBuilder\Enum\DateTimeFormatEnum;
use SabServis\DTOBuilder\Exception\DTOCreationException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class DTODatetimeValueFilter implements DTOValueFilterInterface
{
    public function filter(
        mixed $value,
        DTOBuilderConstructorParameter $parameter,
        bool $valueProvided,
    ): mixed {
        if (is_null($value)) {
            return null;
        }

        $toDateTimeHydrator = $parameter->getAttribute(HydrateToDateTime::class);

        if ($toDateTimeHydrator) {
            return $this->toDatetime($value, $parameter, $toDateTimeHydrator);
        }

        $dateTimeHydrator = $parameter->getAttribute(HydrateDateTime::class);

        if ($dateTimeHydrator) {
            return $this->fromDatetime($value, $parameter, $dateTimeHydrator);
        }

        return $value;
    }

    /** @param DTOBuilderConstructorParameterAttribute<object> $toDateTimeHydrator */
    public function toDatetime(
        mixed $value,
        DTOBuilderConstructorParameter $parameter,
        DTOBuilderConstructorParameterAttribute $toDateTimeHydrator,
    ): DateTimeInterface {
        if (!is_string($value) || trim($value) === '') {
            $list = new ConstraintViolationList();
            $list->add(
                new ConstraintViolation(
                    message: 'Tato hodnota musí být převoditelná na DateTime.',
                    messageTemplate: null,
                    parameters: [],
                    root: $value,
                    propertyPath: $parameter->getName(),
                    invalidValue: $value,
                    code: 'notadatetime',
                ),
            );

            throw new DTOCreationException($list);
        }

        $classname = $toDateTimeHydrator->getArgument('dateTimeClass') ?? DateTime::class;

        try {
            $datetime = new $classname($value);
            assert($datetime instanceof DateTimeInterface);
        } catch (\Throwable $e) {
            $list = new ConstraintViolationList();
            $list->add(
                new ConstraintViolation(
                    message: 'Tato hodnota musí být převoditelná na DateTime.',
                    messageTemplate: null,
                    parameters: [],
                    root: $value,
                    propertyPath: $parameter->getName(),
                    invalidValue: $value,
                    code: 'notadatetime',
                ),
            );

            throw new DTOCreationException($list);
        }

        return $datetime;
    }

    /** @param DTOBuilderConstructorParameterAttribute<object> $dateTimeHydrator */
    public function fromDatetime(
        mixed $value,
        DTOBuilderConstructorParameter $parameter,
        DTOBuilderConstructorParameterAttribute $dateTimeHydrator,
    ): string {
        if (!$value instanceof DateTime && !$value instanceof DateTimeImmutable) {
            $list = new ConstraintViolationList();
            $list->add(
                new ConstraintViolation(
                    message: 'Tato hodnota musí být DateTime.',
                    messageTemplate: null,
                    parameters: [],
                    root: $value,
                    propertyPath: $parameter->getName(),
                    invalidValue: $value,
                    code: 'notadatetime',
                ),
            );

            throw new DTOCreationException($list);
        }

        $format = $dateTimeHydrator->getArgument('format') ?? DateTimeFormatEnum::DateTime;
        assert($format instanceof DateTimeFormatEnum);

        return $value->format($format->value);
    }
}
