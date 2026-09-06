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

        // Nothing to charge: don't involve the payment gateway at all, so a fully-discounted
        // order doesn't depend on how a specific gateway's stub happens to treat a 0 amount.
        if ($breakdown->totalPriceCents > 0) {
            $this->paymentProcessorRegistry
                ->get($paymentProcessorId)
                ->pay($breakdown->totalPriceCents);
        }

        return $breakdown;
    }
}
