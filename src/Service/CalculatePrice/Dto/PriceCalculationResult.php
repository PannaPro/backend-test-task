<?php

namespace App\Service\CalculatePrice\Dto;

final readonly class PriceCalculationResult
{
    public function __construct(
        public int $basePriceInCents,
        public int $priceAfterDiscountsInCents,
        public int $taxAmountInCents,
        public int $finalPriceInCents,
        public int $finalPrice,
        public int $taxRate,
    ) {
    }
}
