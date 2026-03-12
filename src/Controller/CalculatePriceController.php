<?php

namespace App\Controller;

use App\Model\CalculatePriceRequestDto;
use App\Service\CalculatePrice\CalculatePriceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class CalculatePriceController extends AbstractController
{
    public function __construct(
        private CalculatePriceService $calculatePriceService,
    ) {
    }

    #[Route('/calculate-price', name: 'app_calculate_price', methods: ['POST'])]
    public function calculatePrice(
        #[MapRequestPayload] CalculatePriceRequestDto $requestDto,
    ): JsonResponse
    {
        $data = $this->calculatePriceService->calculateProductPrice($requestDto);

        return $this->json($data, Response::HTTP_OK);
    }
}
