<?php

namespace App\Tests\Unit\Service\ProductPricing;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Enum\CouponType;
use App\Service\CalculatePrice\PriceCalculator;
use App\Service\Exception\DomainNotFoundException;
use App\Service\Exception\InvalidRequestDataException;
use App\Service\ProductPricing\ProductPricingService;
use App\Service\TaxRuleProvider;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use PHPUnit\Framework\TestCase;

final class ProductPricingServiceTest extends TestCase
{
    public function testCalculatesPriceWithoutCoupon(): void
    {
        $productRepository = $this->createMock(ProductRepository::class);
        $couponRepository = $this->createMock(CouponRepository::class);
        $service = new ProductPricingService(
            $productRepository,
            $couponRepository,
            new TaxRuleProvider(),
            new PriceCalculator(),
        );

        $productRepository
            ->expects(self::once())
            ->method('getByIdOrFail')
            ->with(1)
            ->willReturn($this->createProduct(10000));

        $couponRepository
            ->expects(self::never())
            ->method('getByCodeOrFail');

        $result = $service->calculate(1, 'DE123456789', null);

        self::assertNull($result->getCoupon());
        self::assertSame('DE123456789', $result->getTaxNumber());
        self::assertSame(11900, $result->getPrice()->getFinalPriceInCents());
        self::assertSame('119.00', $result->getPrice()->getFinalPrice());
        self::assertSame(19, $result->getPrice()->getTaxRate());
    }

    public function testCalculatesPriceWithCouponAndNormalizesCouponCode(): void
    {
        $productRepository = $this->createMock(ProductRepository::class);
        $couponRepository = $this->createMock(CouponRepository::class);
        $service = new ProductPricingService(
            $productRepository,
            $couponRepository,
            new TaxRuleProvider(),
            new PriceCalculator(),
        );

        $coupon = $this->createCoupon(CouponType::PERCENTAGE_OF_PURCHASE, 15, 'D15');

        $productRepository
            ->expects(self::once())
            ->method('getByIdOrFail')
            ->with(1)
            ->willReturn($this->createProduct(10000));

        $couponRepository
            ->expects(self::once())
            ->method('getByCodeOrFail')
            ->with('D15')
            ->willReturn($coupon);

        $result = $service->calculate(1, ' de123456789 ', ' d15 ');

        self::assertSame($coupon, $result->getCoupon());
        self::assertSame('DE123456789', $result->getTaxNumber());
        self::assertSame(10115, $result->getPrice()->getFinalPriceInCents());
        self::assertSame('101.15', $result->getPrice()->getFinalPrice());
    }

    public function testProductNotFoundException(): void
    {
        $productRepository = $this->createMock(ProductRepository::class);
        $couponRepository = $this->createMock(CouponRepository::class);
        $service = new ProductPricingService(
            $productRepository,
            $couponRepository,
            new TaxRuleProvider(),
            new PriceCalculator(),
        );

        $productRepository
            ->expects(self::once())
            ->method('getByIdOrFail')
            ->with(1)
            ->willThrowException(DomainNotFoundException::notFound());

        $this->expectException(DomainNotFoundException::class);

        $service->calculate(1, 'DE123456789', null);
    }

    public function testThrowsExceptionForUnsupportedTaxNumberPrefix(): void
    {
        $productRepository = $this->createMock(ProductRepository::class);
        $couponRepository = $this->createMock(CouponRepository::class);
        $service = new ProductPricingService(
            $productRepository,
            $couponRepository,
            new TaxRuleProvider(),
            new PriceCalculator(),
        );

        $productRepository
            ->expects(self::once())
            ->method('getByIdOrFail')
            ->with(1)
            ->willReturn($this->createProduct(10000));

        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('Unsupported tax number country prefix.');

        $service->calculate(1, 'ES123456789', null);
    }

    private function createProduct(int $priceInCents): Product
    {
        $product = new Product();
        $product->setName('Test product');
        $product->setPrice($priceInCents);
        $product->setCurrency('EUR');

        return $product;
    }

    private function createCoupon(CouponType $type, int $value, string $code): Coupon
    {
        $coupon = new Coupon();
        $coupon->setCode($code);
        $coupon->setType($type);
        $coupon->setValue($value);

        return $coupon;
    }
}
