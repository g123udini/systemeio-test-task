<?php

declare(strict_types=1);

namespace SystemeioTestTask\Tests\Pricing;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SystemeioTestTask\Entity\Coupon;
use SystemeioTestTask\Entity\Product;
use SystemeioTestTask\Enum\CouponType;
use SystemeioTestTask\Pricing\PriceCalculator;

#[CoversClass(PriceCalculator::class)]
final class PriceCalculatorTest extends TestCase
{
    public function testNoCouponAddsTaxOnly(): void
    {
        $product = new Product('Iphone', 10000);

        $breakdown = (new PriceCalculator())->calculate($product, null, 'DE123456789');

        self::assertSame(10000, $breakdown->productPriceCents);
        self::assertSame(0, $breakdown->discountCents);
        self::assertSame(19, $breakdown->taxRatePercent);
        self::assertSame(1900, $breakdown->taxAmountCents);
        self::assertSame(11900, $breakdown->totalPriceCents);
    }

    public function testPercentCouponAppliesDiscountBeforeTax(): void
    {
        // matches the example from the spec: Iphone, Greece, 6% coupon -> 116.56 EUR
        $product = new Product('Iphone', 10000);
        $coupon = new Coupon('TEST6', CouponType::Percent, 6);

        $breakdown = (new PriceCalculator())->calculate($product, $coupon, 'GR123456789');

        self::assertSame(600, $breakdown->discountCents);
        self::assertSame(24, $breakdown->taxRatePercent);
        self::assertSame(2256, $breakdown->taxAmountCents);
        self::assertSame(11656, $breakdown->totalPriceCents);
    }

    public function testFixedCouponIsCappedAtProductPrice(): void
    {
        $product = new Product('Чехол', 1000);
        $coupon = new Coupon('D15', CouponType::Fixed, 1500);

        $breakdown = (new PriceCalculator())->calculate($product, $coupon, 'FRAB123456789');

        self::assertSame(1000, $breakdown->discountCents);
        self::assertSame(0, $breakdown->taxAmountCents);
        self::assertSame(0, $breakdown->totalPriceCents);
    }

    public function testFullPercentCouponZeroesPrice(): void
    {
        $product = new Product('Наушники', 2000);
        $coupon = new Coupon('P100', CouponType::Percent, 100);

        $breakdown = (new PriceCalculator())->calculate($product, $coupon, 'IT12345678900');

        self::assertSame(2000, $breakdown->discountCents);
        self::assertSame(0, $breakdown->taxAmountCents);
        self::assertSame(0, $breakdown->totalPriceCents);
    }

    public function testPercentCouponAboveHundredIsCappedAtProductPrice(): void
    {
        $product = new Product('Наушники', 2000);
        $coupon = new Coupon('OVERSHOOT', CouponType::Percent, 150);

        $breakdown = (new PriceCalculator())->calculate($product, $coupon, 'IT12345678900');

        self::assertSame(2000, $breakdown->discountCents);
        self::assertSame(0, $breakdown->taxAmountCents);
        self::assertSame(0, $breakdown->totalPriceCents);
    }
}
