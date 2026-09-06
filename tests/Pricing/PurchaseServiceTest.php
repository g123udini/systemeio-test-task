<?php

declare(strict_types=1);

namespace SystemeioTestTask\Tests\Pricing;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use SystemeioTestTask\Exception\PaymentFailedException;
use SystemeioTestTask\Pricing\PurchaseService;

#[CoversClass(PurchaseService::class)]
final class PurchaseServiceTest extends KernelTestCase
{
    public function testPurchaseSucceedsViaPaypal(): void
    {
        self::bootKernel();

        $service = self::getContainer()->get(PurchaseService::class);

        $breakdown = $service->purchase(1, 'D15', 'IT12345678900', 'paypal');

        self::assertSame(10370, $breakdown->totalPriceCents);
    }

    public function testPurchaseFailsWhenPaymentDeclined(): void
    {
        self::bootKernel();

        $service = self::getContainer()->get(PurchaseService::class);

        $this->expectException(PaymentFailedException::class);

        $service->purchase(3, null, 'DE123456789', 'stripe');
    }
}
