<?php

declare(strict_types=1);

namespace SystemeioTestTask\Tests\Payment;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;
use SystemeioTestTask\Exception\PaymentFailedException;
use SystemeioTestTask\Payment\PaypalGatewayProcessor;

#[CoversClass(PaypalGatewayProcessor::class)]
final class PaypalGatewayProcessorTest extends TestCase
{
    public function testGetIdentifier(): void
    {
        self::assertSame('paypal', PaypalGatewayProcessor::getIdentifier());
    }

    public function testPaySucceedsBelowThreshold(): void
    {
        $processor = new PaypalGatewayProcessor(new PaypalPaymentProcessor());

        $processor->pay(11656);

        $this->expectNotToPerformAssertions();
    }

    public function testPayWrapsFailureAboveThreshold(): void
    {
        $processor = new PaypalGatewayProcessor(new PaypalPaymentProcessor());

        $this->expectException(PaymentFailedException::class);

        $processor->pay(100001);
    }

    public function testPayDoesNotLeakVendorExceptionMessageToClient(): void
    {
        $processor = new PaypalGatewayProcessor(new PaypalPaymentProcessor());

        try {
            $processor->pay(100001);
            self::fail('Expected a PaymentFailedException.');
        } catch (PaymentFailedException $exception) {
            self::assertStringNotContainsString('Transaction', $exception->getMessage());
            self::assertNotNull($exception->getPrevious());
        }
    }
}
