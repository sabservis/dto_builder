<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Attribute\Assert;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\When;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\LogicException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class RequiredOnValidator extends ConstraintValidator
{
    public function __construct(private ?ExpressionLanguage $expressionLanguage = null)
    {
    }

    public function validate(
        mixed $value,
        Constraint $constraint,
    ): void
    {
        if (!$constraint instanceof When) {
            throw new UnexpectedTypeException($constraint, When::class);
        }

        $context = $this->context;
        $variables = $constraint->values;
        $variables['value'] = $value;
        $variables['this'] = $context->getObject();

        if (!$this->getExpressionLanguage()->evaluate($constraint->expression, $variables)) {
            return;
        }

        $context->getValidator()->inContext($context)
            ->validate($value, $constraint->constraints);
    }

    private function getExpressionLanguage(): ExpressionLanguage
    {
        if (!class_exists(ExpressionLanguage::class)) {
            throw new LogicException(sprintf(
                'The "symfony/expression-language" component is required to use the "%s" validator. '
                        . ' Try running "composer require symfony/expression-language".',
                self::class,
            ));
        }

        return $this->expressionLanguage ??= new ExpressionLanguage();
    }
}
