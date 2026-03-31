<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

final class ApiRoutesTest extends KernelTestCase
{
    public function testCalculatePriceRouteReturnsSuccessForValidPayload(): void
    {
        self::bootKernel();
        $kernel = self::$kernel;
        $container = static::getContainer();

        $product = $this->createProduct(10000);
        $container->set(EntityManagerInterface::class, $this->mockEntityManager(1, $product));

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
                'data' => [
                    'product' => [
                        'id' => null,
                        'name' => 'Test product',
                    ],
                    'taxNumber' => 'DE123456789',
                    'taxRate' => 19,
                    'couponCode' => null,
                    'price' => '119.00',
                    'currency' => 'EUR',
                ],
            ], JSON_THROW_ON_ERROR),
            (string) $response->getContent(),
        );
    }

    public function testPurchaseRouteReturnsValidationErrorForInvalidPayload(): void
    {
        self::bootKernel();
        $kernel = self::$kernel;
        $container = static::getContainer();

        $container->set(EntityManagerInterface::class, $this->mockEntityManager(1, $this->createProduct(5000)));

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

    private function mockEntityManager(int $productId, ?Product $product): EntityManagerInterface
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->with($productId)->willReturn($product);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getClassMetadata')->willReturn($this->createMock(ClassMetadata::class));
        $em->method('getRepository')->willReturn($repository);

        return $em;
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
