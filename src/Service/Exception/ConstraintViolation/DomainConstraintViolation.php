<?php

declare(strict_types=1);

namespace App\Service\Exception\ConstraintViolation;

readonly final class DomainConstraintViolation implements DomainConstraintViolationInterface
{
    public function __construct(
        private string $field,
        private string $message
    ) {}

    public function getField(): string
    {
        return $this->field;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}