<?php

namespace App\Service\CalculatePrice;

use App\Model\CalculatePriceRequestDto;
use App\Service\ProductPricing\ProductPricingService;

final class CalculatePriceService
{
    public function __construct(
        private readonly ProductPricingService $productPricingService,
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
        $pricing = $this->productPricingService->calculate($dto->product, $dto->taxNumber, $dto->couponCode);

        return [
            'product' => [
                'id' => $pricing->getProduct()->getId(),
                'name' => $pricing->getProduct()->getName(),
            ],
            'taxNumber' => $dto->taxNumber,
            'taxRate' => $pricing->getPrice()->getTaxRate(),
            'couponCode' => $pricing->getCoupon()?->getCode(),
            'price' => $pricing->getPrice()->getFinalPrice(),
            'currency' => $pricing->getProduct()->getCurrency(),
        ];
    }
}
