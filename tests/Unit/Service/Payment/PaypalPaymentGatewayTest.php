<?php

namespace App\Tests\Unit\Service\Payment;

use App\Service\Exception\PaymentFailedException;
use App\Service\Payment\PaypalPaymentGateway;
use PHPUnit\Framework\TestCase;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

final class PaypalPaymentGatewayTest extends TestCase
{
    public function testSupportsPaypalProcessor(): void
    {
        $gateway = new PaypalPaymentGateway($this->createMock(PaypalPaymentProcessor::class));

        self::assertTrue($gateway->supports('paypal'));
        self::assertFalse($gateway->supports('stripe'));
    }

    public function testCanChargeOnlyPositiveAmountsWithinProcessorLimit(): void
    {
        $gateway = new PaypalPaymentGateway($this->createMock(PaypalPaymentProcessor::class));

        self::assertFalse($gateway->canCharge(0));
        self::assertTrue($gateway->canCharge(1));
        self::assertTrue($gateway->canCharge(100000));
        self::assertFalse($gateway->canCharge(100001));
    }

    public function testChargesPaypalProcessor(): void
    {
        $processor = $this->createMock(PaypalPaymentProcessor::class);
        $gateway = new PaypalPaymentGateway($processor);

        $processor
            ->expects(self::once())
            ->method('pay')
            ->with(11900);

        $gateway->charge(11900);
    }

    public function testWrapsProcessorException(): void
    {
        $processor = $this->createMock(PaypalPaymentProcessor::class);
        $gateway = new PaypalPaymentGateway($processor);

        $processor
            ->expects(self::once())
            ->method('pay')
            ->willThrowException(new \Exception('PayPal failure'));

        $this->expectException(PaymentFailedException::class);
        $this->expectExceptionMessage('PayPal failure');

        $gateway->charge(11900);
    }
}
