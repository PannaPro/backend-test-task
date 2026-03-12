<?php

namespace App\Service;

use App\Dto\CalculatePriceRequestDto;

class CalculatePriceService
{
    public function calculate(CalculatePriceRequestDto $dto): array
    {
        return [
            'product' => $dto->product,
            'taxNumber' => $dto->taxNumber,
            'couponCode' => $dto->couponCode,
            'price' => 0,
        ];
    }
}