<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO\Builder\Filter;

use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorParameter;

class DTODefaultValueFilter implements DTOValueFilterInterface
{
    public function filter(
        mixed $value,
        DTOBuilderConstructorParameter $parameter,
        bool $valueProvided,
    ): mixed {
        $useDefault =
            $parameter->isDefaultValueAvailable() &&
            (
                !$valueProvided
                || (is_null($value) && !$parameter->allowsNull()
                )
            );

        return $useDefault ? $parameter->getDefaultValue() : $value;
    }
}
