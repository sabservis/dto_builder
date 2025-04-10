<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Helper;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorParameter;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorParameterType;
use SabServis\DTOBuilder\Exception\ServiceNotFoundException;

class DIResolver
{
    public function __construct(
        private readonly ContainerInterface $serviceManager,
    ) {
    }

    /**
     * Resolve a parameter to a value.
     *
     * Returns a callback for resolving a parameter to a value, but without
     * allowing mapping array `$config` arguments to the `config` service.
     *
     */
    public function resolveParameterCallback(
        string $requestedName,
    ): callable {
        /**
         * @param DTOBuilderConstructorParameter|ReflectionParameter $parameter
         * @return mixed
         * @throws ServiceNotFoundException If type-hinted parameter cannot be
         *   resolved to a service in the container.
         */
        return fn (DTOBuilderConstructorParameter|ReflectionParameter $parameter) => $this->resolveParameter($parameter, $requestedName);
    }

    /**
     * Logic common to all parameter resolution.
     *
     * @throws ServiceNotFoundException If type-hinted parameter cannot be
     * @throws \ReflectionException
     *   resolved to a service in the container.
     */
    private function resolveParameter(
        DTOBuilderConstructorParameter|ReflectionParameter $parameter,
        string $requestedName,
    ): mixed {
        $class = $this->resolveParameterClass($parameter);

        if (!$class) {
            throw new ServiceNotFoundException(
                sprintf(
                    'Unable to create service "%s"; unable to resolve parameter "%s" '
                    . 'to a class, interface, or array type',
                    $requestedName,
                    $parameter->getName(),
                ),
            );
        }

        $type = $class->getName();

        if ($this->serviceManager->has($type)) {
            return $this->serviceManager->get($type);
        }

        if (!$parameter->isOptional()) {
            throw new ServiceNotFoundException(
                sprintf(
                    'Unable to create service "%s"; unable to resolve parameter "%s" using type hint "%s"',
                    $requestedName,
                    $parameter->getName(),
                    $type,
                ),
            );
        }

        // Type not available in container, but the value is optional and has a
        // default defined.
        return $parameter->getDefaultValue();
    }

    /** @return ReflectionClass<object>|null */
    private function resolveParameterClass(DTOBuilderConstructorParameter|ReflectionParameter $parameter): ?ReflectionClass
    {
        if (
            !(
                $parameter->getType() instanceof DTOBuilderConstructorParameterType
                || $parameter->getType() instanceof ReflectionNamedType
            )
        ) {
            return null;
        }

        /** @var class-string $className */
        $className = $parameter->getType()->getName() && !$parameter->getType()->isBuiltin()
            ? $parameter->getType()->getName()
            : null;

        return $className ? new ReflectionClass($className) : null;
    }
}
