<?php

namespace App\Service\ProductPricing\Dto;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Service\CalculatePrice\Dto\PriceCalculationResult;

final readonly class OrderPricingResult
{
    public function __construct(
        public Product $product,
        public ?Coupon $coupon,
        public string $taxNumber,
        public PriceCalculationResult $price,
    ) {
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getCoupon(): ?Coupon
    {
        return $this->coupon;
    }

    public function getTaxNumber(): string
    {
        return $this->taxNumber;
    }

    public function getPrice(): PriceCalculationResult
    {
        return $this->price;
    }
}
