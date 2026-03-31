<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class ValidCouponCode extends Constraint
{
    public string $message = 'Coupon "{{ code }}" not found.';
}
