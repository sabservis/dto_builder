<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO\Builder;

use ReflectionException;
use SabServis\DTOBuilder\Exception\DTOCreationException;

class DTOMultiArrayBuilder extends DTOBuilder
{
    /**
     * @template T of \SabServis\DTOBuilder\DTO\AbstractDTO
     * @param class-string<T> $className
     * @param array<mixed> $data
     * @return list<T>
     * @throws DTOCreationException
     * @throws ReflectionException
     */
    public function build(
        string $className,
        array $data,
    ): array {
        $return = [];

        $builder = $this->serviceLocator->get(DTOArrayBuilder::class);

        foreach ($data as $item) {
            $return[] = $builder->build($className, (array)$item);
        }

        return $return;
    }
}
