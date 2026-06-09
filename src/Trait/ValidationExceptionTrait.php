<?php

declare(strict_types=1);

namespace App\Trait;

use ApiPlatform\Validator\Exception\ValidationException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

trait ValidationExceptionTrait
{
    private function throwValidationError(string $property, string $message, mixed $root = null): void
    {
        $violations = new ConstraintViolationList([
            new ConstraintViolation($message, null, [], $root, $property, null),
        ]);

        throw new ValidationException($violations);
    }
}