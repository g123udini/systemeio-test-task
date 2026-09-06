<?php

declare(strict_types=1);

namespace SystemeioTestTask\Tests\Payment;

use InvalidArgumentException;
use LogicException;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SystemeioTestTask\Payment\PaymentProcessorInterface;
use SystemeioTestTask\Payment\PaymentProcessorRegistry;

#[CoversClass(PaymentProcessorRegistry::class)]
final class PaymentProcessorRegistryTest extends TestCase
{
    public function testHasAndGetResolveByIdentifier(): void
    {
        $processor = new class implements PaymentProcessorInterface {
            #[Override]
            public static function getIdentifier(): string
            {
                return 'fake';
            }

            #[Override]
            public function pay(int $amountCents): void
            {
            }
        };

        $registry = new PaymentProcessorRegistry([$processor]);

        self::assertTrue($registry->has('fake'));
        self::assertFalse($registry->has('unknown'));
        self::assertSame($processor, $registry->get('fake'));
        self::assertSame(['fake'], $registry->getSupportedIdentifiers());
    }

    public function testGetThrowsForUnsupportedIdentifier(): void
    {
        $registry = new PaymentProcessorRegistry([]);

        $this->expectException(InvalidArgumentException::class);

        $registry->get('unknown');
    }

    public function testConstructorThrowsOnDuplicateIdentifier(): void
    {
        $makeFake = static fn (): PaymentProcessorInterface => new class implements PaymentProcessorInterface {
            #[Override]
            public static function getIdentifier(): string
            {
                return 'duplicate';
            }

            #[Override]
            public function pay(int $amountCents): void
            {
            }
        };

        $this->expectException(LogicException::class);

        new PaymentProcessorRegistry([$makeFake(), $makeFake()]);
    }
}
