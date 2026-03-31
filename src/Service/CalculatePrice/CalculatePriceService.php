<?php

declare(strict_types=1);

namespace App\Service\CalculatePrice;

use App\Model\CalculatePriceRequestDto;
use App\ResponseHandling\ResponseCollection\ResponseCollection;
use App\Service\ProductPricing\ProductPricingService;

final class CalculatePriceService
{
    public function __construct(
        private readonly ProductPricingService $productPricingService,
    ) {
    }

    public function calculateProductPrice(CalculatePriceRequestDto $dto): ResponseCollection
    {
        $pricing = $this->productPricingService->calculate(
            $dto->getProduct(),
            $dto->getTaxNumber(),
            $dto->getCoupon(),
        );

        return new ResponseCollection(
            [
                'product' => [
                    'id' => $pricing->getProduct()->getId(),
                    'name' => $pricing->getProduct()->getName(),
                ],
                'taxNumber' => $pricing->getTaxNumber(),
                'taxRate' => $pricing->getPrice()->getTaxRate(),
                'couponCode' => $pricing->getCoupon()?->getCode(),
                'price' => $pricing->getPrice()->getFinalPrice(),
                'currency' => $pricing->getProduct()->getCurrency(),
            ]
        );
    }
}
