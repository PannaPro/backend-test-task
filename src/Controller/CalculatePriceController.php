<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\CalculatePriceRequestDto;
use App\ResponseHandling\ResponseCollection\ResponseCollectionInterface;
use App\Service\CalculatePrice\CalculatePriceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class CalculatePriceController extends AbstractController
{
    public function __construct(
        private readonly CalculatePriceService $calculatePriceService,
    ) {
    }

    #[Route('/calculate-price', name: 'app_calculate_price', methods: ['POST'])]
    public function calculatePrice(
        #[MapRequestPayload] CalculatePriceRequestDto $requestDto,
    ): ResponseCollectionInterface
    {
        return $this->calculatePriceService->calculateProductPrice($requestDto);
    }
}
