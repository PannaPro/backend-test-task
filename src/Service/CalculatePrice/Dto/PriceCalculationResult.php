<?php

declare(strict_types=1);

namespace App\Service\CalculatePrice\Dto;

final readonly class PriceCalculationResult
{
    public function __construct(
        public int $basePriceInCents,
        public int $priceAfterDiscountsInCents,
        public int $taxAmountInCents,
        public int $finalPriceInCents,
        public string $finalPrice,
        public int $taxRate,
    ) {
    }

    public function getBasePriceInCents(): int
    {
        return $this->basePriceInCents;
    }

    public function getPriceAfterDiscountsInCents(): int
    {
        return $this->priceAfterDiscountsInCents;
    }

    public function getTaxAmountInCents(): int
    {
        return $this->taxAmountInCents;
    }

    public function getFinalPriceInCents(): int
    {
        return $this->finalPriceInCents;
    }

    public function getFinalPrice(): string
    {
        return $this->finalPrice;
    }

    public function getTaxRate(): int
    {
        return $this->taxRate;
    }
}
