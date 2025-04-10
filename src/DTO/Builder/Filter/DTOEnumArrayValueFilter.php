<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO\Builder\Filter;

use Exception;
use SabServis\DTOBuilder\Attribute\HydrateEnumArray;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorParameter;

class DTOEnumArrayValueFilter implements DTOValueFilterInterface
{
    public function filter(
        mixed $value,
        DTOBuilderConstructorParameter $parameter,
        bool $valueProvided,
    ): mixed {
        if (!is_array($value)) {
            return $value;
        }

        $parameterType = $parameter->getType()?->getName();

        if ($parameterType === 'array') {
            $columnHydrator = $parameter->getAttribute(HydrateEnumArray::class);

            if ($columnHydrator) {
                /** @var class-string $enumClass */
                $enumClass = $columnHydrator->getArgument('class') ?? null;

                if ($enumClass) {
                    if (method_exists($enumClass, 'fromName') === false) {
                        throw new Exception(sprintf('Enum %s has no method fromName', $enumClass));
                    }

                    return array_map(static fn (string $val) => $enumClass::fromName($val), $value);
                }
            }
        }

        return $value;
    }
}
