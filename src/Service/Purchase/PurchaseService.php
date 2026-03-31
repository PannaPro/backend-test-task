<?php

declare(strict_types=1);

namespace App\Service\Purchase;

use App\Model\PurchaseRequestDto;
use App\ResponseHandling\ResponseCollection\ResponseCollection;
use App\Service\Exception\PaymentFailedException;
use App\Service\Payment\PaymentGatewayResolver;
use App\Service\ProductPricing\ProductPricingService;

final class PurchaseService
{
    public function __construct(
        private readonly ProductPricingService $orderPricingService,
        private readonly PaymentGatewayResolver $paymentGatewayResolver,
    ) {
    }

    public function purchase(PurchaseRequestDto $dto): ResponseCollection
    {
        $pricing = $this->orderPricingService->calculate(
            $dto->getProduct(),
            $dto->getTaxNumber(),
            $dto->getCoupon(),
        );

        if ($pricing->getPrice()->getFinalPriceInCents() === 0) {
            return new ResponseCollection(['status' => 'ok']);
        }

        $paymentGateway = $this->paymentGatewayResolver->resolve($dto->getPaymentProcessor());
        if (!$paymentGateway->canCharge($pricing->getPrice()->getFinalPriceInCents())) {
            throw PaymentFailedException::because('Final price is below or above the allowed amount for the selected payment processor.');
        }

        $paymentGateway->charge($pricing->getPrice()->getFinalPriceInCents());

        return new ResponseCollection(['status' => 'ok']);
    }
}
