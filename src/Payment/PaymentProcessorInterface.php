<?php

declare(strict_types=1);

namespace SystemeioTestTask\Payment;

use SystemeioTestTask\Exception\PaymentFailedException;

interface PaymentProcessorInterface
{
    public static function getIdentifier(): string;

    /**
     * @throws PaymentFailedException
     */
    public function pay(int $amountCents): void;
}
