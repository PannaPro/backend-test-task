<?php

namespace App\Tests\Unit\Service\Payment;

use App\Service\Exception\PaymentFailedException;
use App\Service\Payment\StripePaymentGateway;
use PHPUnit\Framework\TestCase;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

final class StripePaymentGatewayTest extends TestCase
{
    public function testSupportsStripeProcessor(): void
    {
        $gateway = new StripePaymentGateway($this->createMock(StripePaymentProcessor::class));

        self::assertTrue($gateway->supports('stripe'));
        self::assertFalse($gateway->supports('paypal'));
    }

    public function testCanChargeOnlyAmountsAcceptedByStripeSdk(): void
    {
        $gateway = new StripePaymentGateway($this->createMock(StripePaymentProcessor::class));

        self::assertFalse($gateway->canCharge(9999));
        self::assertTrue($gateway->canCharge(10000));
    }

    public function testConvertsCentsToCurrencyAmountBeforeCharging(): void
    {
        $processor = $this->createMock(StripePaymentProcessor::class);
        $gateway = new StripePaymentGateway($processor);

        $processor
            ->expects(self::once())
            ->method('processPayment')
            ->with(119.0)
            ->willReturn(true);

        $gateway->charge(11900);
    }

    public function testThrowsExceptionWhenStripeDeclinesPayment(): void
    {
        $processor = $this->createMock(StripePaymentProcessor::class);
        $gateway = new StripePaymentGateway($processor);

        $processor
            ->expects(self::once())
            ->method('processPayment')
            ->with(119.0)
            ->willReturn(false);

        $this->expectException(PaymentFailedException::class);
        $this->expectExceptionMessage('Stripe payment was declined.');

        $gateway->charge(11900);
    }
}
