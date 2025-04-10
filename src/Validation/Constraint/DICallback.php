<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Validation\Constraint;

use SabServis\DTOBuilder\Validation\Validator\DICallbackValidator;
use Symfony\Component\Validator\Constraints\Callback;

/**
 * Same as Symfony Assert\Callback but adds automatic Dependency injection into
 * targeted functions
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class DICallback extends Callback
{
    /**
     * @inheritDoc
     */
    public function validatedBy()
    {
        return DICallbackValidator::class;
    }
}
