<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Attribute\Assert;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\When;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class RequiredOn extends When
{
    public function __construct(
        string $property,
        mixed $value,
        ?bool $allowNull = null,
    )
    {
        parent::__construct(
            expression: sprintf('isArray ? this.%s in conditionValue : this.%s === conditionValue', $property, $property),
            constraints: new NotBlank(allowNull: $allowNull),
            values: ['conditionValue' => $value, 'isArray' => is_array($value)],
        );
    }
}
