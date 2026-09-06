<?php

declare(strict_types=1);

namespace SystemeioTestTask\Pricing;

use LogicException;
use SystemeioTestTask\Repository\CouponRepository;
use SystemeioTestTask\Repository\ProductRepository;

final class CalculatePriceService
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly CouponRepository $couponRepository,
        private readonly PriceCalculator $priceCalculator,
    ) {
    }

    public function calculate(int $productId, ?string $couponCode, string $taxNumber): PriceBreakdown
    {
        $product = $this->productRepository->find($productId)
            ?? throw new LogicException('Product not found despite passing validation.');

        $coupon = null !== $couponCode
            ? $this->couponRepository->findOneByCode($couponCode)
            : null;

        return $this->priceCalculator->calculate($product, $coupon, $taxNumber);
    }
}
