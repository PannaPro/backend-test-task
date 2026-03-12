<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ValidTaxNumberValidator extends ConstraintValidator
{
    /** TODO кеш будет хранить патерны (например ключ буквенныый префикс - значение % налоговой ставки) */
    private const PATTERNS = [
        'DE' => '/^DE\d{9}$/',
        'IT' => '/^IT\d{11}$/',
        'GR' => '/^GR\d{9}$/',
        'FR' => '/^FR[A-Z]{2}\d{9}$/',
    ];

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

        $value = mb_strtoupper(trim($value));

        foreach (self::PATTERNS as $pattern) {
            if (preg_match($pattern, $value) === 1) {
                return;
            }
        }

        $this->context
            ->buildViolation($constraint->message)
            ->addViolation();
    }
}