<?php

declare(strict_types=1);

namespace App\Service\Exception;

use App\Service\Exception\ConstraintViolation\DomainConstraintViolationInterface;

abstract class DomainConstraintViolationListException extends DomainException
{
    /**
     * @var DomainConstraintViolationInterface[]
     */
    private array $violations = [];

    public function addViolation(DomainConstraintViolationInterface $violation)
    {
        $this->violations[] = $violation;
    }

    /**
     * @return DomainConstraintViolationInterface[]
     */
    public function getViolations(): array
    {
        return $this->violations;
    }
}