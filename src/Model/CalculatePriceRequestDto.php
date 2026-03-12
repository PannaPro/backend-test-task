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
        public ?int $productId = null,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[ValidTaxNumber]
        public ?string $taxNumber = null,

        #[Assert\Type('string')]
        #[Assert\NotBlank(allowNull: true)]
        public ?string $couponCode = null,
    ) {
    }
}