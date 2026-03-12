<?php

namespace App\Service\Payment;

use App\Enum\PaymentGatewayType;
use App\Service\Exception\PaymentFailedException;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

final class StripePaymentGateway implements PaymentGatewayInterface
{
    public function __construct(
        private readonly StripePaymentProcessor $paymentProcessor,
    ) {
    }

    public function supports(string $processor): bool
    {
        return $processor === PaymentGatewayType::STRIPE->value;
    }

    public function canCharge(int $amountInCents): bool
    {
        return ($amountInCents / 100) >= 100;
    }

    public function charge(int $amountInCents): void
    {
        $amount = $amountInCents / 100;
        $isSuccessful = $this->paymentProcessor->processPayment($amount);

        if ($isSuccessful) {
            return;
        }

        throw PaymentFailedException::because('Stripe payment was declined.');
    }
}
