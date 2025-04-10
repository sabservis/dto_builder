<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO\Builder\Filter;

use SabServis\DTOBuilder\Attribute\HydrateDateTime;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorParameter;
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

        $dateTimeHydrator = $parameter->getAttribute(HydrateDateTime::class);

        if ($dateTimeHydrator) {
            if (!$value instanceof \DateTime && !$value instanceof \DateTimeImmutable) {
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
            \assert($format instanceof DateTimeFormatEnum);

            return $value->format($format->value);
        }

        return $value;
    }
}
