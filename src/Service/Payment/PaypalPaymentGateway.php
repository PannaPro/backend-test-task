<?php

namespace App\Service\Payment;

use App\Enum\PaymentGatewayType;
use App\Service\Exception\PaymentFailedException;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

final class PaypalPaymentGateway implements PaymentGatewayInterface
{
    public function __construct(
        private readonly PaypalPaymentProcessor $paymentProcessor,
    ) {
    }

    public function supports(string $processor): bool
    {
        return $processor === PaymentGatewayType::PAYPAL->value;
    }

    public function canCharge(int $amountInCents): bool
    {
        return $amountInCents > 0 && $amountInCents <= 100000;
    }

    public function charge(int $amountInCents): void
    {
        try {
            $this->paymentProcessor->pay($amountInCents);
        } catch (\Exception $exception) {
            throw PaymentFailedException::because($exception->getMessage());
        }
    }
}
