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
        public ?int $product = null,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[ValidTaxNumber]
        public ?string $taxNumber = null,

        #[Assert\Type('string')]
        #[Assert\NotBlank(allowNull: true)]
        public ?string $couponCode = null,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Choice(callback: [PaymentGatewayType::class, 'values'])]
        public ?string $paymentProcessor = null,
    ) {
    }
}
