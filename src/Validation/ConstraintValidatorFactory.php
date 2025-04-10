<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Validation;

use SabServis\DTOBuilder\Validation\Constraint\DICallback;
use SabServis\DTOBuilder\Validation\Validator\DICallbackValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;

/**
 * Default implementation of the ConstraintValidatorFactoryInterface.
 *
 * This enforces the convention that the validatedBy() method on any
 * Constraint will return the class name of the ConstraintValidator that
 * should validate the Constraint.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class ConstraintValidatorFactory extends \Symfony\Component\Validator\ConstraintValidatorFactory
{
    public function __construct(
        private readonly \Psr\Container\ContainerInterface $container,
    ) {
        parent::__construct();
    }

    public function getInstance(Constraint $constraint): ConstraintValidatorInterface
    {
        if ($constraint instanceof DICallback) {
            return $this->container->get(DICallbackValidator::class);
        }

        return parent::getInstance($constraint);
    }
}
