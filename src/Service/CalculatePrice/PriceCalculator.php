<?php

declare(strict_types=1);

namespace App\Service\CalculatePrice;

use App\Entity\Coupon;
use App\Enum\CouponType;
use App\Service\CalculatePrice\Dto\PriceCalculationResult;

final class PriceCalculator
{
    public function calculateFinalPrice(int $basePriceInCents, int $taxRate, ?Coupon $coupon): PriceCalculationResult
    {
        $priceAfterDiscountsInCents = $this->applyCoupon($basePriceInCents, $coupon);
        $taxAmountInCents = (int) round($priceAfterDiscountsInCents * $taxRate / 100);

        $finalPriceInCents = $priceAfterDiscountsInCents + $taxAmountInCents;

        $finalPrice = $this->mapPriceFromPriceInCents($finalPriceInCents);

        return new PriceCalculationResult(
            basePriceInCents: $basePriceInCents,
            priceAfterDiscountsInCents: $priceAfterDiscountsInCents,
            taxAmountInCents: $taxAmountInCents,
            finalPriceInCents: $finalPriceInCents,
            finalPrice: $finalPrice,
            taxRate: $taxRate,
        );
    }

    private function applyCoupon(int $basePriceInCents, ?Coupon $coupon): int
    {
        if ($coupon === null) {
            return $basePriceInCents;
        }

        $discountedPrice = match ($coupon->getType()) {
            CouponType::FIXED_DISCOUNT => $basePriceInCents - $coupon->getValue(),
            CouponType::PERCENTAGE_OF_PURCHASE => (int)round($basePriceInCents * (1 - $coupon->getValue() / 100)),
        };

        return max(0, $discountedPrice);
    }

    private function mapPriceFromPriceInCents(int $priceInCents): string
    {
        return number_format($priceInCents / 100, 2, '.', '');
    }
}
