<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Validation\Validator;

use SabServis\DTOBuilder\Validation\Constraint\ConditionalValid;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates a value based on conditional criteria defined in the Constraint.
 *
 * @param mixed $value The value to be validated.
 * @param Constraint $constraint The Constraint object that defines the validation criteria.
 *
 * @throws UnexpectedTypeException if $constraint is not an instance of ConditionalValid.
 */
class ConditionalValidValidator extends ConstraintValidator
{
    public function validate(
        mixed $value,
        Constraint $constraint,
    ): void {
        if (!$constraint instanceof ConditionalValid) {
            throw new UnexpectedTypeException($constraint, ConditionalValid::class);
        }

        $object = $this->context->getObject();
        $fieldValue = $object->{$constraint->field};

        if ($constraint->condition === ConditionalValid::CONDITION_EQUAL) {
            if ($fieldValue !== $constraint->value) {
                return;
            }
        } elseif ($constraint->condition === ConditionalValid::CONDITION_NOT_EQUAL) {
            if ($fieldValue === $constraint->value) {
                return;
            }
        } else {
            throw new UnexpectedTypeException($constraint, ConditionalValid::class);
        }

        $validator = new Valid();
        $this->context->getValidator()->inContext($this->context)->validate($value, $validator);
    }
}
