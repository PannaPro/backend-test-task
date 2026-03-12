<?php

namespace App\Service\CalculatePrice;

use App\Model\CalculatePriceRequestDto;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Service\TaxRuleProvider;

final class CalculatePriceService
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly CouponRepository $couponRepository,
        private readonly PriceCalculator $priceCalculator,
        private readonly TaxRuleProvider $taxRuleProvider,
    ) {
    }

    /**
     * @return array{
     *     product: array{id: int|null, name: string|null},
     *     taxNumber: string,
     *     taxRate: int,
     *     couponCode: string|null,
     *     price: string,
     *     currency: string|null
     * }
     */
    public function calculateProductPrice(CalculatePriceRequestDto $dto): array
    {
        $product = $this->productRepository->getByIdOrFail($dto->productId);
        $taxRate = $this->taxRuleProvider->getTaxRateByTaxNumber($dto->taxNumber);
        $coupon = $dto->couponCode !== null
            ? $this->couponRepository->getByCodeOrFail(strtoupper($dto->couponCode))
            : null;

        $price = $this->priceCalculator->calculateFinalPrice($product->getPrice(), $taxRate, $coupon);

        return [
            'product' => [
                'id' => $product->getId(),
                'name' => $product->getName(),
            ],
            'taxNumber' => $dto->taxNumber,
            'taxRate' => $price->taxRate,
            'couponCode' => $coupon?->getCode(),
            'price' => $price->finalPrice,
            'currency' => $product->getCurrency(),
        ];
    }
}
