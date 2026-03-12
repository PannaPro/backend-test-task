<?php

namespace App\Tests\Unit\Service\CalculatePrice;

use App\Entity\Coupon;
use App\Enum\CouponType;
use App\Service\CalculatePrice\PriceCalculator;
use PHPUnit\Framework\TestCase;

final class PriceCalculatorTest extends TestCase
{
    public function testCalculatesFinalPriceWithoutCoupon(): void
    {
        $calculator = new PriceCalculator();

        $result = $calculator->calculateFinalPrice(10000, 19, null);

        self::assertSame(10000, $result->getBasePriceInCents());
        self::assertSame(10000, $result->getPriceAfterDiscountsInCents());
        self::assertSame(1900, $result->getTaxAmountInCents());
        self::assertSame(11900, $result->getFinalPriceInCents());
        self::assertSame('119.00', $result->getFinalPrice());
        self::assertSame(19, $result->getTaxRate());
    }

    public function testCalculatesFinalPriceWithPercentageCoupon(): void
    {
        $calculator = new PriceCalculator();
        $coupon = $this->createCoupon(CouponType::PERCENTAGE_OF_PURCHASE, 15);

        $result = $calculator->calculateFinalPrice(10000, 19, $coupon);

        self::assertSame(8500, $result->getPriceAfterDiscountsInCents());
        self::assertSame(1615, $result->getTaxAmountInCents());
        self::assertSame(10115, $result->getFinalPriceInCents());
        self::assertSame('101.15', $result->getFinalPrice());
    }

    public function testCalculatesFinalPriceWithFixedCoupon(): void
    {
        $calculator = new PriceCalculator();
        $coupon = $this->createCoupon(CouponType::FIXED_DISCOUNT, 500);

        $result = $calculator->calculateFinalPrice(2000, 24, $coupon);

        self::assertSame(1500, $result->getPriceAfterDiscountsInCents());
        self::assertSame(360, $result->getTaxAmountInCents());
        self::assertSame(1860, $result->getFinalPriceInCents());
        self::assertSame('18.60', $result->getFinalPrice());
    }

    public function testDoesNotAllowDiscountedPriceToGoBelowZero(): void
    {
        $calculator = new PriceCalculator();
        $coupon = $this->createCoupon(CouponType::FIXED_DISCOUNT, 5000);

        $result = $calculator->calculateFinalPrice(1000, 22, $coupon);

        self::assertSame(0, $result->getPriceAfterDiscountsInCents());
        self::assertSame(0, $result->getTaxAmountInCents());
        self::assertSame(0, $result->getFinalPriceInCents());
        self::assertSame('0.00', $result->getFinalPrice());
    }

    private function createCoupon(CouponType $type, int $value): Coupon
    {
        $coupon = new Coupon();
        $coupon->setCode('TEST');
        $coupon->setType($type);
        $coupon->setValue($value);

        return $coupon;
    }
}
