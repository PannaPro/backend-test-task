<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\ProductPricing;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Enum\CouponType;
use App\Service\CalculatePrice\PriceCalculator;
use App\Service\Exception\InvalidRequestDataException;
use App\Service\ProductPricing\ProductPricingService;
use App\Service\TaxRuleProvider;
use PHPUnit\Framework\TestCase;

final class ProductPricingServiceTest extends TestCase
{
    private ProductPricingService $service;

    protected function setUp(): void
    {
        $this->service = new ProductPricingService(
            new TaxRuleProvider(),
            new PriceCalculator(),
        );
    }

    public function testCalculatesPriceWithoutCoupon(): void
    {
        $result = $this->service->calculate(
            $this->createProduct(10000),
            'DE123456789',
            null,
        );

        self::assertNull($result->getCoupon());
        self::assertSame('DE123456789', $result->getTaxNumber());
        self::assertSame(11900, $result->getPrice()->getFinalPriceInCents());
        self::assertSame('119.00', $result->getPrice()->getFinalPrice());
        self::assertSame(19, $result->getPrice()->getTaxRate());
    }

    public function testCalculatesPriceWithCoupon(): void
    {
        $coupon = $this->createCoupon(CouponType::PERCENTAGE_OF_PURCHASE, 15, 'D15');

        $result = $this->service->calculate(
            $this->createProduct(10000),
            'DE123456789',
            $coupon,
        );

        self::assertSame($coupon, $result->getCoupon());
        self::assertSame('DE123456789', $result->getTaxNumber());
        self::assertSame(10115, $result->getPrice()->getFinalPriceInCents());
        self::assertSame('101.15', $result->getPrice()->getFinalPrice());
    }

    public function testThrowsExceptionForUnsupportedTaxNumberPrefix(): void
    {
        $this->expectException(InvalidRequestDataException::class);
        $this->expectExceptionMessage('Unsupported tax number country prefix.');

        $this->service->calculate(
            $this->createProduct(10000),
            'ES123456789',
            null,
        );
    }

    private function createProduct(int $priceInCents): Product
    {
        $product = new Product();
        $product->setName('Test product');
        $product->setPrice($priceInCents);

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
