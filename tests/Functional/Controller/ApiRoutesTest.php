<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Product;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Service\CalculatePrice\PriceCalculator;
use App\Service\ProductPricing\ProductPricingService;
use App\Service\TaxRuleProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

final class ApiRoutesTest extends KernelTestCase
{
    public function testCalculatePriceRouteReturnsSuccessForValidPayload(): void
    {
        self::bootKernel();
        $kernel = self::$kernel;

        $container = static::getContainer();

        $productRepository = $this->createMock(ProductRepository::class);
        $couponRepository = $this->createMock(CouponRepository::class);
        $productRepository
            ->method('getByIdOrFail')
            ->with(1)
            ->willReturn($this->createProduct(10000));
        $couponRepository
            ->expects(self::never())
            ->method('getByCodeOrFail');

        $container->set(ProductPricingService::class, new ProductPricingService(
            $productRepository,
            $couponRepository,
            new TaxRuleProvider(),
            new PriceCalculator(),
        ));

        $request = Request::create(
            '/calculate-price',
            'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'product' => 1,
                'taxNumber' => 'DE123456789',
                'couponCode' => null,
            ], JSON_THROW_ON_ERROR),
        );
        $response = $kernel->handle($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/json', $response->headers->get('content-type'));
        self::assertJsonStringEqualsJsonString(
            json_encode([
                'product' => [
                    'id' => null,
                    'name' => 'Test product',
                ],
                'taxNumber' => 'DE123456789',
                'taxRate' => 19,
                'couponCode' => null,
                'price' => '119.00',
                'currency' => 'EUR',
            ], JSON_THROW_ON_ERROR),
            (string) $response->getContent(),
        );
    }

    public function testPurchaseRouteReturnsValidationErrorForInvalidPayload(): void
    {
        self::bootKernel();
        $kernel = self::$kernel;

        $request = Request::create(
            '/purchase',
            'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'product' => 1,
                'taxNumber' => 'INVALID',
                'couponCode' => null,
                'paymentProcessor' => 'paypal',
            ], JSON_THROW_ON_ERROR),
        );
        $response = $kernel->handle($request);

        self::assertSame(400, $response->getStatusCode());
        self::assertSame('application/problem+json', $response->headers->get('content-type'));
    }

    private function createProduct(int $priceInCents): Product
    {
        $product = new Product();
        $product->setName('Test product');
        $product->setPrice($priceInCents);
        $product->setCurrency('EUR');

        return $product;
    }
}
