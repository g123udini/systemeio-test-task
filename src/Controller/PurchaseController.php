<?php

declare(strict_types=1);

namespace SystemeioTestTask\Controller;

use DDH\ComponentBundle\Response\ResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use SystemeioTestTask\Pricing\PurchaseService;
use SystemeioTestTask\Request\PurchaseRequest;

final class PurchaseController
{
    public function __construct(
        private readonly PurchaseService $purchaseService,
        private readonly ResponseFactory $responseFactory,
    ) {
    }

    #[Route('/purchase', name: 'purchase', methods: ['POST'])]
    public function __invoke(PurchaseRequest $request): JsonResponse
    {
        $breakdown = $this->purchaseService->purchase(
            $request->getProductId(),
            $request->getCouponCode(),
            $request->getTaxNumber(),
            $request->getPaymentProcessorId(),
        );

        return $this->responseFactory->create($breakdown);
    }
}
