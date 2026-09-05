<?php

declare(strict_types=1);

namespace SystemeioTestTask\Payment;

use InvalidArgumentException;
use LogicException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use function array_key_exists;
use function array_keys;
use function sprintf;

final class PaymentProcessorRegistry
{
    /** @var array<string, PaymentProcessorInterface> */
    private readonly array $processorsByIdentifier;

    /**
     * @param iterable<PaymentProcessorInterface> $processors
     */
    public function __construct(#[AutowireIterator('app.payment_processor')] iterable $processors)
    {
        $map = [];

        foreach ($processors as $processor) {
            $identifier = $processor::getIdentifier();

            if (array_key_exists($identifier, $map)) {
                throw new LogicException(sprintf('Payment processor identifier "%s" is registered by more than one processor.', $identifier));
            }

            $map[$identifier] = $processor;
        }

        $this->processorsByIdentifier = $map;
    }

    public function has(string $identifier): bool
    {
        return isset($this->processorsByIdentifier[$identifier]);
    }

    public function get(string $identifier): PaymentProcessorInterface
    {
        return $this->processorsByIdentifier[$identifier]
            ?? throw new InvalidArgumentException(sprintf('Unsupported payment processor "%s".', $identifier));
    }

    /**
     * @return list<string>
     */
    public function getSupportedIdentifiers(): array
    {
        return array_keys($this->processorsByIdentifier);
    }
}
