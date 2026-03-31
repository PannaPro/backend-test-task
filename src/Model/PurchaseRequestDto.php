<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Enum\PaymentGatewayType;
use App\Validator\ValidCouponCode;
use App\Validator\ValidTaxNumber;
use Symfony\Component\Validator\Constraints as Assert;

final class PurchaseRequestDto
{
    private ?Coupon $coupon = null;

    public function __construct(
        #[Assert\NotNull(message: 'Product not found.')]
        private readonly ?Product $product = null,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[ValidTaxNumber]
        private readonly ?string $taxNumber = null,

        #[Assert\Type('string')]
        #[Assert\NotBlank(allowNull: true)]
        #[ValidCouponCode]
        private readonly ?string $couponCode = null,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Choice(callback: [PaymentGatewayType::class, 'values'])]
        private readonly ?string $paymentProcessor = null,
    ) {
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function getTaxNumber(): ?string
    {
        return $this->taxNumber;
    }

    public function getCoupon(): ?Coupon
    {
        return $this->coupon;
    }

    public function setCoupon(Coupon $coupon): void
    {
        $this->coupon = $coupon;
    }

    public function getPaymentProcessor(): ?string
    {
        return $this->paymentProcessor;
    }
}
