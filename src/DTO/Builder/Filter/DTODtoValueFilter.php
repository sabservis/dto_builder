<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO\Builder\Filter;

use SabServis\DTOBuilder\DTO\AbstractDTO;
use SabServis\DTOBuilder\DTO\Builder\DTOArrayBuilder;
use SabServis\DTOBuilder\DTO\Builder\DTOEntityBuilder;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorParameter;

/**
 * Automatically hydrates array to DTO
 */
class DTODtoValueFilter implements DTOValueFilterInterface
{
    public function __construct(
        private readonly DTOArrayBuilder $arrayBuilder,
        private readonly DTOEntityBuilder $entityBuilder,
    ) {
    }

    public function filter(
        mixed $value,
        DTOBuilderConstructorParameter $parameter,
        bool $valueProvided,
    ): mixed {
        if (is_null($value)) {
            return null;
        }

        $parameterType = $parameter->getType()?->getName();

        if (
            $parameterType &&
            class_exists($parameterType) &&
            is_subclass_of($parameterType, AbstractDTO::class)
        ) {
            if (is_array($value)) {
                $value = $this->arrayBuilder->build($parameterType, $value);
            } elseif (!is_subclass_of($value, AbstractDTO::class)) {
                $value = $this->entityBuilder->build($parameterType, $value);
            } elseif ($parameterType !== $value::class && !($value instanceof $parameterType)) {
                $value = $this->entityBuilder->build($parameterType, $value);
            }
        }

        return $value;
    }
}
