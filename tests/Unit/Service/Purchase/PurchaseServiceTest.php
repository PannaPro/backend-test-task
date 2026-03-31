<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Purchase;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Enum\CouponType;
use App\Model\PurchaseRequestDto;
use App\Service\CalculatePrice\PriceCalculator;
use App\Service\Exception\PaymentFailedException;
use App\Service\Payment\PaymentGatewayInterface;
use App\Service\Payment\PaymentGatewayResolver;
use App\Service\ProductPricing\ProductPricingService;
use App\Service\Purchase\PurchaseService;
use App\Service\TaxRuleProvider;
use PHPUnit\Framework\TestCase;

final class PurchaseServiceTest extends TestCase
{
    private ProductPricingService $pricingService;

    protected function setUp(): void
    {
        $this->pricingService = new ProductPricingService(
            new TaxRuleProvider(),
            new PriceCalculator(),
        );
    }

    public function testSkipsPaymentForFreeOrder(): void
    {
        $paymentGateway = $this->createMock(PaymentGatewayInterface::class);
        $service = new PurchaseService($this->pricingService, new PaymentGatewayResolver([$paymentGateway]));

        $paymentGateway
            ->expects(self::never())
            ->method('supports');

        $dto = $this->createDto($this->createProduct(1000), $this->createCoupon(CouponType::FIXED_DISCOUNT, 5000));
        $service->purchase($dto);
    }

    public function testThrowsExceptionWhenGatewayCannotChargeAmount(): void
    {
        $paymentGateway = $this->createMock(PaymentGatewayInterface::class);
        $service = new PurchaseService($this->pricingService, new PaymentGatewayResolver([$paymentGateway]));

        $paymentGateway->method('supports')->with('paypal')->willReturn(true);
        $paymentGateway->expects(self::once())->method('canCharge')->with(5950)->willReturn(false);
        $paymentGateway->expects(self::never())->method('charge');

        $this->expectException(PaymentFailedException::class);

        $service->purchase($this->createDto($this->createProduct(5000), null));
    }

    public function testChargesGatewayWhenAmountIsAllowed(): void
    {
        $paymentGateway = $this->createMock(PaymentGatewayInterface::class);
        $service = new PurchaseService($this->pricingService, new PaymentGatewayResolver([$paymentGateway]));

        $paymentGateway->method('supports')->with('paypal')->willReturn(true);
        $paymentGateway->expects(self::once())->method('canCharge')->with(11900)->willReturn(true);
        $paymentGateway->expects(self::once())->method('charge')->with(11900);

        $service->purchase($this->createDto($this->createProduct(10000), null));
    }

    private function createDto(Product $product, ?Coupon $coupon): PurchaseRequestDto
    {
        $dto = new PurchaseRequestDto(
            product: $product,
            taxNumber: 'DE123456789',
            couponCode: null,
            paymentProcessor: 'paypal',
        );

        if ($coupon !== null) {
            $dto->setCoupon($coupon);
        }

        return $dto;
    }

    private function createProduct(int $priceInCents): Product
    {
        $product = new Product();
        $product->setName('Test product');
        $product->setPrice($priceInCents);
        $product->setCurrency('EUR');

        return $product;
    }

    private function createCoupon(CouponType $type, int $value): Coupon
    {
        $coupon = new Coupon();
        $coupon->setCode('D15');
        $coupon->setType($type);
        $coupon->setValue($value);

        return $coupon;
    }
}
