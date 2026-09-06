<?php

declare(strict_types=1);

namespace SystemeioTestTask\Controller;

use DDH\ComponentBundle\Response\ResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use SystemeioTestTask\Pricing\CalculatePriceService;
use SystemeioTestTask\Request\CalculatePriceRequest;

final class CalculatePriceController
{
    public function __construct(
        private readonly CalculatePriceService $calculatePriceService,
        private readonly ResponseFactory $responseFactory,
    ) {
    }

    #[Route('/calculate-price', name: 'calculate_price', methods: ['POST'])]
    public function __invoke(CalculatePriceRequest $request): JsonResponse
    {
        $breakdown = $this->calculatePriceService->calculate(
            $request->getProductId(),
            $request->getCouponCode(),
            $request->getTaxNumber(),
        );

        return $this->responseFactory->create($breakdown);
    }
}
