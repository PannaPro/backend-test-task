<?php

namespace App\Model;

use App\Validator\ValidTaxNumber;
use Symfony\Component\Validator\Constraints as Assert;

class CalculatePriceRequestDto
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
}