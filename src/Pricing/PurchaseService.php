<?php

declare(strict_types=1);

namespace SystemeioTestTask\Pricing;

use SystemeioTestTask\Payment\PaymentProcessorRegistry;

final class PurchaseService
{
    public function __construct(
        private readonly CalculatePriceService $calculatePriceService,
        private readonly PaymentProcessorRegistry $paymentProcessorRegistry,
    ) {
    }

    public function purchase(int $productId, ?string $couponCode, string $taxNumber, string $paymentProcessorId): PriceBreakdown
    {
        $breakdown = $this->calculatePriceService->calculate($productId, $couponCode, $taxNumber);

        $this->paymentProcessorRegistry
            ->get($paymentProcessorId)
            ->pay($breakdown->totalPriceCents);

        return $breakdown;
    }
}
