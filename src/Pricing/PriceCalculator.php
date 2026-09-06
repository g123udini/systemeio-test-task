<?php

declare(strict_types=1);

namespace SystemeioTestTask\Pricing;

use LogicException;
use SystemeioTestTask\Entity\Coupon;
use SystemeioTestTask\Entity\Product;
use SystemeioTestTask\Enum\CouponType;
use SystemeioTestTask\Tax\EuCountry;
use function sprintf;

final class PriceCalculator
{
    public function calculate(Product $product, ?Coupon $coupon, string $taxNumber): PriceBreakdown
    {
        $basePriceCents = $product->getPriceCents();
        $discountCents = $this->calculateDiscount($basePriceCents, $coupon);
        $priceAfterDiscountCents = $basePriceCents - $discountCents;

        $country = EuCountry::fromTaxNumber($taxNumber)
            ?? throw new LogicException(sprintf('Unsupported tax number "%s" reached the price calculator unvalidated.', $taxNumber));

        $taxRatePercent = $country->taxRatePercent();
        $taxAmountCents = (int) round($priceAfterDiscountCents * $taxRatePercent / 100);

        return new PriceBreakdown(
            productPriceCents: $basePriceCents,
            discountCents: $discountCents,
            taxRatePercent: $taxRatePercent,
            taxAmountCents: $taxAmountCents,
            totalPriceCents: $priceAfterDiscountCents + $taxAmountCents,
        );
    }

    private function calculateDiscount(int $basePriceCents, ?Coupon $coupon): int
    {
        if (null === $coupon) {
            return 0;
        }

        $rawDiscountCents = match ($coupon->getType()) {
            CouponType::Fixed => $coupon->getValue(),
            CouponType::Percent => (int) round($basePriceCents * $coupon->getValue() / 100),
        };

        return min($rawDiscountCents, $basePriceCents);
    }
}
