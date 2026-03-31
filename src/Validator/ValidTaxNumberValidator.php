<?php

declare(strict_types=1);

namespace App\Validator;

use App\Service\TaxRuleProvider;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ValidTaxNumberValidator extends ConstraintValidator
{
    public function __construct(
        private readonly TaxRuleProvider $taxRuleProvider,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidTaxNumber) {
            throw new UnexpectedTypeException($constraint, ValidTaxNumber::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_string($value)) {
            return;
        }

        if ($this->taxRuleProvider->isValidTaxNumber($value)) {
            return;
        }

        $this->context
            ->buildViolation($constraint->message)
            ->addViolation();
    }
}