<?php

declare(strict_types=1);

namespace App\Service\Exception\ConstraintViolation;

use Symfony\Component\Validator\ConstraintViolationInterface;

readonly final class SymfonyViolationAdapter implements DomainConstraintViolationInterface
{
    public function __construct(
        private ConstraintViolationInterface $violation
    ) {}

    public function getField(): string
    {
        return $this->violation->getPropertyPath();
    }

    public function getMessage(): string
    {
        return $this->violation->getMessage();
    }
}