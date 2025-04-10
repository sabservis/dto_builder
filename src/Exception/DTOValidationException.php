<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

class DTOValidationException extends \Exception
{
    public mixed $payload = null;

    public function __construct(
        private readonly ConstraintViolationListInterface $errors,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): ConstraintViolationListInterface
    {
        return $this->errors;
    }
}
