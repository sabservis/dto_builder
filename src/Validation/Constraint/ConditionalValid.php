<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Validation\Constraint;

use SabServis\DTOBuilder\Validation\Validator\ConditionalValidValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Symfony validation
 * Same as Assert\Valid, but you can specify condition when Assert\Valid is called.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ConditionalValid extends Constraint
{
    /** @var string If field equals value */
    public const CONDITION_EQUAL = 'equal';

    /** @var string If field not equals value */
    public const CONDITION_NOT_EQUAL = 'not_equal';

    public function __construct(
        public string $field,
        public mixed $value,
        public string $condition = self::CONDITION_EQUAL,
        mixed $options = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct($options, $groups, $payload);
    }

    public function validatedBy(): string
    {
        return ConditionalValidValidator::class;
    }
}
