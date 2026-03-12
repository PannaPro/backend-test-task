<?php

namespace App\Service\OrderPricing;

use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Service\CalculatePrice\PriceCalculator;
use App\Service\OrderPricing\Dto\OrderPricingResult;
use App\Service\TaxRuleProvider;

final class ProductPricingService
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly CouponRepository $couponRepository,
        private readonly TaxRuleProvider $taxRuleProvider,
        private readonly PriceCalculator $priceCalculator,
    ) {
    }

    public function calculate(int $productId, string $taxNumber, ?string $couponCode): OrderPricingResult
    {
        $product = $this->productRepository->getByIdOrFail($productId);
        $taxRate = $this->taxRuleProvider->getTaxRateByTaxNumber($taxNumber);
        $coupon = $couponCode !== null
            ? $this->couponRepository->getByCodeOrFail(strtoupper($couponCode))
            : null;

        $price = $this->priceCalculator->calculateFinalPrice($product->getPrice(), $taxRate, $coupon);

        return new OrderPricingResult(
            product: $product,
            coupon: $coupon,
            price: $price,
        );
    }
}
