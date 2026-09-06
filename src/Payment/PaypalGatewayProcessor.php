<?php

declare(strict_types=1);

namespace SystemeioTestTask\Payment;

use Override;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;
use SystemeioTestTask\Exception\PaymentFailedException;
use Throwable;

#[AutoconfigureTag('app.payment_processor')]
final class PaypalGatewayProcessor implements PaymentProcessorInterface
{
    public function __construct(private readonly PaypalPaymentProcessor $processor)
    {
    }

    #[Override]
    public static function getIdentifier(): string
    {
        return 'paypal';
    }

    #[Override]
    public function pay(int $amountCents): void
    {
        try {
            $this->processor->pay($amountCents);
        } catch (Throwable $exception) {
            // $exception is kept as $previous for logging; its message may contain internal
            // transaction details and must not be forwarded to the client verbatim.
            throw new PaymentFailedException(self::getIdentifier(), 'Payment was declined.', $exception);
        }
    }
}
