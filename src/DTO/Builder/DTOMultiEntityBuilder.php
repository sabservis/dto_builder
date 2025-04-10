<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO\Builder;

use Psr\Container\ContainerInterface;
use ReflectionException;
use SabServis\DTOBuilder\Exception\DTOCreationException;
use SabServis\DTOBuilder\Helper\DIResolver;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DTOMultiEntityBuilder extends DTOBuilder
{
    public function __construct(
        private readonly DTOEntityBuilder $dtoEntityBuilder,
        ?ValidatorInterface $validator,
        ContainerInterface $serviceLocator,
        DIResolver $DIResolver,
    )
    {
        parent::__construct($validator, $serviceLocator, $DIResolver);
    }

    /**
     * @template T of \SabServis\DTOBuilder\DTO\AbstractDTO
     * @param class-string<T> $className
     * @return list<T>
     * @throws DTOCreationException
     * @throws ReflectionException
     */
    public function build(
        string $className,
        mixed $data,
    ): array {
        $return = [];

        foreach ($data as $item) {
            $return[] = $this->dtoEntityBuilder->build($className, $item);
        }

        return $return;
    }
}
