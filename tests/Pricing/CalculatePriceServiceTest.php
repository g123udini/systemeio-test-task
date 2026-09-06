<?php

declare(strict_types=1);

namespace SystemeioTestTask\Tests\Pricing;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use SystemeioTestTask\Pricing\CalculatePriceService;

#[CoversClass(CalculatePriceService::class)]
final class CalculatePriceServiceTest extends KernelTestCase
{
    public function testCalculatesPriceForSeededProductWithoutCoupon(): void
    {
        self::bootKernel();

        $service = self::getContainer()->get(CalculatePriceService::class);

        $breakdown = $service->calculate(1, null, 'DE123456789');

        self::assertSame(11900, $breakdown->totalPriceCents);
    }

    public function testCalculatesPriceForSeededProductAndCoupon(): void
    {
        self::bootKernel();

        $service = self::getContainer()->get(CalculatePriceService::class);

        $breakdown = $service->calculate(1, 'P10', 'GR123456789');

        self::assertSame(11160, $breakdown->totalPriceCents);
    }
}
