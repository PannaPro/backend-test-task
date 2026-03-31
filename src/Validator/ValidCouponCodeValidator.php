<?php

declare(strict_types=1);

namespace App\Validator;

use App\Repository\CouponRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ValidCouponCodeValidator extends ConstraintValidator
{
    public function __construct(
        private readonly CouponRepository $couponRepository,
    ) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidCouponCode) {
            throw new UnexpectedTypeException($constraint, ValidCouponCode::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        $coupon = $this->couponRepository->findOneBy(['code' => $value]);

        if ($coupon === null) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ code }}', $value)
                ->addViolation();
            return;
        }

        $this->context->getObject()->setCoupon($coupon);
    }
}
