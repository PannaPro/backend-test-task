<?php

declare(strict_types=1);

namespace App\Service\ProductPricing;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Service\CalculatePrice\PriceCalculator;
use App\Service\ProductPricing\Dto\OrderPricingResult;
use App\Service\TaxRuleProvider;

final class ProductPricingService
{
    public function __construct(
        private readonly TaxRuleProvider $taxRuleProvider,
        private readonly PriceCalculator $priceCalculator,
    ) {
    }

    public function calculate(Product $product, string $taxNumber, ?Coupon $coupon): OrderPricingResult
    {
        $taxRate = $this->taxRuleProvider->getTaxRateByTaxNumber($taxNumber);
        $price = $this->priceCalculator->calculateFinalPrice($product->getPrice(), $taxRate, $coupon);

        return new OrderPricingResult(
            product: $product,
            coupon: $coupon,
            taxNumber: $taxNumber,
            price: $price,
        );
    }
}
