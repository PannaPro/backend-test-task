<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class ValidTaxNumber extends Constraint
{
    public string $message = 'Invalid tax number format.';
}