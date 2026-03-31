<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Service\Exception\InvalidRequestDataException;

final class PaymentGatewayResolver
{
    /**
     * @param iterable<PaymentGatewayInterface> $paymentGateways
     */
    public function __construct(
        private readonly iterable $paymentGateways,
    ) {
    }

    public function resolve(string $processor): PaymentGatewayInterface
    {
        $normalizedProcessor = strtolower(trim($processor));

        foreach ($this->paymentGateways as $paymentGateway) {
            if ($paymentGateway->supports($normalizedProcessor)) {
                return $paymentGateway;
            }
        }

        throw InvalidRequestDataException::because(sprintf('Unsupported payment processor "%s".', $processor));
    }
}
