<?php

declare(strict_types=1);

namespace SystemeioTestTask\Tests\Payment;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;
use SystemeioTestTask\Exception\PaymentFailedException;
use SystemeioTestTask\Payment\StripeGatewayProcessor;

#[CoversClass(StripeGatewayProcessor::class)]
final class StripeGatewayProcessorTest extends TestCase
{
    public function testGetIdentifier(): void
    {
        self::assertSame('stripe', StripeGatewayProcessor::getIdentifier());
    }

    public function testPaySucceedsAtOrAboveThreshold(): void
    {
        $processor = new StripeGatewayProcessor(new StripePaymentProcessor());

        $processor->pay(10000);

        $this->expectNotToPerformAssertions();
    }

    public function testPayFailsBelowThreshold(): void
    {
        $processor = new StripeGatewayProcessor(new StripePaymentProcessor());

        $this->expectException(PaymentFailedException::class);

        $processor->pay(1000);
    }
}
