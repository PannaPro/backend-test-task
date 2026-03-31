<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\PurchaseRequestDto;
use App\ResponseHandling\ResponseCollection\ResponseCollectionInterface;
use App\Service\Purchase\PurchaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class PurchaseController extends AbstractController
{
    public function __construct(
        private readonly PurchaseService $purchaseService,
    ) {
    }

    #[Route('/purchase', name: 'app_purchase', methods: ['POST'])]
    public function purchase(
        #[MapRequestPayload] PurchaseRequestDto $requestDto,
    ): ResponseCollectionInterface
    {
        return $this->purchaseService->purchase($requestDto);
    }
}
