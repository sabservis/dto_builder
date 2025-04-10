<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Validation\Validator;

use Psr\Container\ContainerInterface;
use SabServis\DTOBuilder\Helper\DIResolver;
use SabServis\DTOBuilder\Validation\Constraint\DICallback;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validator for Callback constraint with automatic dependency injection into given functions
 *
 */
class DICallbackValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ContainerInterface $serviceManager,
        private readonly DIResolver $DIResolver,
    ) {
    }

    public function validate(
        mixed $value,
        Constraint $constraint,
    ): void {
        $this->validateConstraint($constraint);
        assert($constraint instanceof DICallback);
        $method = $constraint->callback;

        if ($method instanceof \Closure) {
            $method($value, $this->context, $constraint->payload, $this->serviceManager);
        } elseif (\is_array($method)) {
            if (!\is_callable($method)) {
                if (isset($method[0]) && \is_object($method[0])) {
                    $method[0] = $method[0]::class;
                }

                throw new ConstraintDefinitionException(
                    json_encode($method) . ' targeted by Callback constraint is not a valid callable.',
                );
            }

            $reflMethod = new \ReflectionMethod($method[0], $method[1]);
            $reflectionParameters = $reflMethod->getParameters();
            unset($reflectionParameters[0]);
            unset($reflectionParameters[1]);

            if ($reflMethod->isStatic()) {
                unset($reflectionParameters[2]);
            }

            $test = $this->DIResolver->resolveParameterCallback($method[0]);
            $parameters = array_map($test, $reflectionParameters);
            $method($value, $this->context, $constraint->payload, ...$parameters);
        } elseif ($value !== null) {
            assert(is_string($method));
            $this->callObjectFunction($method, $value, $constraint);
        }
    }

    private function callObjectFunction(
        string $method,
        mixed $object,
        DICallback $constraint,
    ): void {
        if (!method_exists($object, $method)) {
            throw new ConstraintDefinitionException(
                sprintf(
                    'Method "%s" targeted by Callback constraint does not exist in class "%s".',
                    $method,
                    get_debug_type($object),
                ),
            );
        }

        $reflMethod = new \ReflectionMethod($object, $method);

        $reflectionParameters = $reflMethod->getParameters();
        unset($reflectionParameters[0]);
        unset($reflectionParameters[1]);

        if ($reflMethod->isStatic()) {
            unset($reflectionParameters[2]);
        }

        $params = $this->DIResolver->resolveParameterCallback($object::class);
        $parameters = array_map($params, $reflectionParameters);

        if ($reflMethod->isStatic()) {
            $reflMethod->invoke(null, $object, $this->context, $constraint->payload, ...$parameters);
        } else {
            $reflMethod->invoke($object, $this->context, $constraint->payload, ...$parameters);
        }
    }

    private function validateConstraint(Constraint $constraint,): void
    {
        if (!$constraint instanceof DICallback) {
            throw new UnexpectedTypeException($constraint, DICallback::class);
        }
    }
}
