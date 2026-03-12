<?php

namespace App\Service\OrderPricing\Dto;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Service\CalculatePrice\Dto\PriceCalculationResult;

final readonly class OrderPricingResult
{
    public function __construct(
        public Product $product,
        public ?Coupon $coupon,
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

    public function getPrice(): PriceCalculationResult
    {
        return $this->price;
    }
}
