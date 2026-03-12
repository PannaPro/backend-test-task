<?php

namespace App\Tests\Unit\Service\Purchase;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Enum\CouponType;
use App\Model\PurchaseRequestDto;
use App\Service\Exception\PaymentFailedException;
use App\Service\CalculatePrice\PriceCalculator;
use App\Service\Payment\PaymentGatewayInterface;
use App\Service\Payment\PaymentGatewayResolver;
use App\Service\ProductPricing\ProductPricingService;
use App\Service\Purchase\PurchaseService;
use App\Service\TaxRuleProvider;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use PHPUnit\Framework\TestCase;

final class PurchaseServiceTest extends TestCase
{
    public function testSkipsPaymentForFreeOrder(): void
    {
        $pricingService = $this->createProductPricingService(
            product: $this->createProduct(1000),
            coupon: $this->createCoupon(CouponType::FIXED_DISCOUNT, 5000),
        );
        $paymentGateway = $this->createMock(PaymentGatewayInterface::class);
        $paymentGatewayResolver = new PaymentGatewayResolver([$paymentGateway]);
        $service = new PurchaseService($pricingService, $paymentGatewayResolver);

        $paymentGateway
            ->expects(self::never())
            ->method('supports');

        $service->purchase($this->createPurchaseRequestDto(couponCode: 'D15'));
    }

    public function testThrowsExceptionWhenGatewayCannotChargeAmount(): void
    {
        $pricingService = $this->createProductPricingService(
            product: $this->createProduct(5000),
            coupon: null,
        );
        $paymentGateway = $this->createMock(PaymentGatewayInterface::class);
        $paymentGatewayResolver = new PaymentGatewayResolver([$paymentGateway]);
        $service = new PurchaseService($pricingService, $paymentGatewayResolver);

        $paymentGateway
            ->expects(self::once())
            ->method('supports')
            ->with('paypal')
            ->willReturn(true);

        $paymentGateway
            ->expects(self::once())
            ->method('canCharge')
            ->with(5950)
            ->willReturn(false);

        $paymentGateway
            ->expects(self::never())
            ->method('charge');

        $this->expectException(PaymentFailedException::class);

        $service->purchase($this->createPurchaseRequestDto(couponCode: null));
    }

    public function testChargesGatewayWhenAmountIsAllowed(): void
    {
        $pricingService = $this->createProductPricingService(
            product: $this->createProduct(10000),
            coupon: null,
        );
        $paymentGateway = $this->createMock(PaymentGatewayInterface::class);
        $paymentGatewayResolver = new PaymentGatewayResolver([$paymentGateway]);
        $service = new PurchaseService($pricingService, $paymentGatewayResolver);

        $paymentGateway
            ->expects(self::once())
            ->method('supports')
            ->with('paypal')
            ->willReturn(true);

        $paymentGateway
            ->expects(self::once())
            ->method('canCharge')
            ->with(11900)
            ->willReturn(true);

        $paymentGateway
            ->expects(self::once())
            ->method('charge')
            ->with(11900);

        $service->purchase($this->createPurchaseRequestDto(couponCode: null));
    }

    private function createProductPricingService(Product $product, ?Coupon $coupon): ProductPricingService
    {
        $productRepository = $this->createMock(ProductRepository::class);
        $couponRepository = $this->createMock(CouponRepository::class);

        $productRepository
            ->method('getByIdOrFail')
            ->with(1)
            ->willReturn($product);

        if ($coupon !== null) {
            $couponRepository
                ->method('getByCodeOrFail')
                ->with('D15')
                ->willReturn($coupon);
        }

        return new ProductPricingService(
            $productRepository,
            $couponRepository,
            new TaxRuleProvider(),
            new PriceCalculator(),
        );
    }

    private function createPurchaseRequestDto(?string $couponCode): PurchaseRequestDto
    {
        return new PurchaseRequestDto(
            product: 1,
            taxNumber: 'DE123456789',
            couponCode: $couponCode,
            paymentProcessor: 'paypal',
        );
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
