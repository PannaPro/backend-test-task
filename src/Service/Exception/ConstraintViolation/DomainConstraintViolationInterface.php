<?php

declare(strict_types=1);

namespace App\Service\Exception\ConstraintViolation;

interface DomainConstraintViolationInterface
{
    public function getField(): string;

    public function getMessage(): string;
}