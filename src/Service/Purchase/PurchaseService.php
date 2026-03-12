<?php

namespace App\Service\Purchase;

use App\Model\PurchaseRequestDto;
use App\Service\Exception\PaymentFailedException;
use App\Service\ProductPricing\ProductPricingService;
use App\Service\Payment\PaymentGatewayResolver;

final class PurchaseService
{
    public function __construct(
        private readonly ProductPricingService  $orderPricingService,
        private readonly PaymentGatewayResolver $paymentGatewayResolver,
    ) {
    }

    public function purchase(PurchaseRequestDto $dto): void
    {
        $pricing = $this->orderPricingService->calculate($dto->product, $dto->taxNumber, $dto->couponCode);
        if ($pricing->getPrice()->getFinalPriceInCents() === 0) {
            return;
        }

        $paymentGateway = $this->paymentGatewayResolver->resolve($dto->paymentProcessor);
        if (!$paymentGateway->canCharge($pricing->price->finalPriceInCents)) {
            throw PaymentFailedException::because('Final price is below or above the allowed amount for the selected payment processor.');
        }

        $paymentGateway->charge($pricing->price->finalPriceInCents);
    }
}
