<?php

declare(strict_types=1);

namespace SystemeioTestTask\Exception;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;
use function sprintf;

final class PaymentFailedException extends UnprocessableEntityHttpException
{
    public function __construct(string $processorIdentifier, string $reason, ?Throwable $previous = null)
    {
        parent::__construct(sprintf('Payment via "%s" failed: %s', $processorIdentifier, $reason), $previous);
    }
}
