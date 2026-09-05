<?php

declare(strict_types=1);

namespace SystemeioTestTask\Tests\Validator\Constraint;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use SystemeioTestTask\Validator\Constraint\PaymentProcessorSupported;
use SystemeioTestTask\Validator\Constraint\PaymentProcessorSupportedValidator;

#[CoversClass(PaymentProcessorSupportedValidator::class)]
final class PaymentProcessorSupportedValidatorTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testPaypalIsSupported(): void
    {
        self::assertCount(0, $this->validator->validate('paypal', new PaymentProcessorSupported()));
    }

    public function testStripeIsSupported(): void
    {
        self::assertCount(0, $this->validator->validate('stripe', new PaymentProcessorSupported()));
    }

    public function testUnsupportedProcessorRaisesViolation(): void
    {
        $violations = $this->validator->validate('unknown', new PaymentProcessorSupported());

        self::assertCount(1, $violations);
    }

    public function testNullValueRaisesNoViolation(): void
    {
        self::assertCount(0, $this->validator->validate(null, new PaymentProcessorSupported()));
    }
}
