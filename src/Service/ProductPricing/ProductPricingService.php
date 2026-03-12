<?php

namespace App\Service\ProductPricing;

use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Service\CalculatePrice\PriceCalculator;
use App\Service\ProductPricing\Dto\OrderPricingResult;
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
        $normalizedTaxNumber = strtoupper(trim($taxNumber));
        $product = $this->productRepository->getByIdOrFail($productId);
        $taxRate = $this->taxRuleProvider->getTaxRateByTaxNumber($normalizedTaxNumber);
        $coupon = $couponCode !== null
            ? $this->couponRepository->getByCodeOrFail(strtoupper(trim($couponCode)))
            : null;

        $price = $this->priceCalculator->calculateFinalPrice($product->getPrice(), $taxRate, $coupon);

        return new OrderPricingResult(
            product: $product,
            coupon: $coupon,
            taxNumber: $normalizedTaxNumber,
            price: $price,
        );
    }
}
