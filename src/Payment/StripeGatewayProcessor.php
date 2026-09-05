<?php

declare(strict_types=1);

namespace SystemeioTestTask\Payment;

use Override;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;
use SystemeioTestTask\Exception\PaymentFailedException;
use Throwable;

#[AutoconfigureTag('app.payment_processor')]
final class StripeGatewayProcessor implements PaymentProcessorInterface
{
    public function __construct(private readonly StripePaymentProcessor $processor)
    {
    }

    #[Override]
    public static function getIdentifier(): string
    {
        return 'stripe';
    }

    #[Override]
    public function pay(int $amountCents): void
    {
        try {
            $succeeded = $this->processor->processPayment($amountCents / 100);
        } catch (Throwable $exception) {
            throw new PaymentFailedException(self::getIdentifier(), 'Payment was declined.', $exception);
        }

        if (!$succeeded) {
            throw new PaymentFailedException(self::getIdentifier(), 'Payment was declined.');
        }
    }
}
