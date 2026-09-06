<?php

declare(strict_types=1);

namespace SystemeioTestTask\Tests\Pricing;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SystemeioTestTask\Pricing\PriceBreakdown;

#[CoversClass(PriceBreakdown::class)]
final class PriceBreakdownTest extends TestCase
{
    public function testJsonSerializeConvertsCentsToEuros(): void
    {
        $breakdown = new PriceBreakdown(
            productPriceCents: 10000,
            discountCents: 600,
            taxRatePercent: 24,
            taxAmountCents: 2256,
            totalPriceCents: 11656,
        );

        self::assertSame(
            [
                'productPrice' => 100.0,
                'discount' => 6.0,
                'taxRatePercent' => 24,
                'taxAmount' => 22.56,
                'price' => 116.56,
            ],
            $breakdown->jsonSerialize(),
        );
    }
}
