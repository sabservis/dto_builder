<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO\Builder\Filter;

use SabServis\DTOBuilder\Attribute\HydrateColumn;
use SabServis\DTOBuilder\DTO\AbstractDTO;
use SabServis\DTOBuilder\DTO\Builder\DTOMultiArrayBuilder;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorParameter;

class DTOArrayValueFilter implements DTOValueFilterInterface
{
    public function __construct(
        private readonly DTOMultiArrayBuilder $multiArrayBuilder,
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

        if ($parameterType === 'array') {
            $columnHydrator = $parameter->getAttribute(HydrateColumn::class);

            if ($columnHydrator) {
                //its array - recursive create dto
                /** @var class-string<AbstractDTO> $arrayTarget */
                $arrayTarget = $columnHydrator->getArgument('arrayTarget') ?? null;

                if ($arrayTarget) {
                    if (!is_array($value) && method_exists($arrayTarget, 'toArray')) {
                        $value = $value->toArray();
                    }

                    $value = $this->multiArrayBuilder->build($arrayTarget, $value);
                }
            }
        }

        return $value;
    }
}
