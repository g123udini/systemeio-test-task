<?php

declare(strict_types=1);

namespace SystemeioTestTask\Controller;

use DDH\ComponentBundle\Response\ResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use SystemeioTestTask\Payment\PaymentProcessorRegistry;
use SystemeioTestTask\Pricing\PriceBreakdownResolver;
use SystemeioTestTask\Request\PurchaseRequest;

final class PurchaseController
{
    public function __construct(
        private readonly PriceBreakdownResolver $priceBreakdownResolver,
        private readonly PaymentProcessorRegistry $paymentProcessorRegistry,
        private readonly ResponseFactory $responseFactory,
    ) {
    }

    #[Route('/purchase', name: 'purchase', methods: ['POST'])]
    public function __invoke(PurchaseRequest $request): JsonResponse
    {
        $breakdown = $this->priceBreakdownResolver->resolve(
            $request->getProductId(),
            $request->getCouponCode(),
            $request->getTaxNumber(),
        );

        $this->paymentProcessorRegistry
            ->get($request->getPaymentProcessorId())
            ->pay($breakdown->totalPriceCents);

        return $this->responseFactory->create($breakdown);
    }
}
