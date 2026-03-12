<?php

namespace App\Model;

use App\Enum\PaymentGatewayType;
use App\Validator\ValidTaxNumber;
use Symfony\Component\Validator\Constraints as Assert;

class PurchaseRequestDto
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Type('integer')]
        #[Assert\Positive]
        private ?int $product = null,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[ValidTaxNumber]
        private ?string $taxNumber = null,

        #[Assert\Type('string')]
        #[Assert\NotBlank(allowNull: true)]
        private ?string $couponCode = null,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Choice(callback: [PaymentGatewayType::class, 'values'])]
        private ?string $paymentProcessor = null,
    ) {
    }

    public function getProduct(): ?int
    {
        return $this->product;
    }

    public function getTaxNumber(): ?string
    {
        return $this->taxNumber;
    }

    public function getCouponCode(): ?string
    {
        return $this->couponCode;
    }

    public function getPaymentProcessor(): ?string
    {
        return $this->paymentProcessor;
    }
}
