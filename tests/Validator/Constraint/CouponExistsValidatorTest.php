<?php

declare(strict_types=1);

namespace SystemeioTestTask\Tests\Validator\Constraint;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use SystemeioTestTask\Validator\Constraint\CouponExists;
use SystemeioTestTask\Validator\Constraint\CouponExistsValidator;

#[CoversClass(CouponExistsValidator::class)]
final class CouponExistsValidatorTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testExistingCouponRaisesNoViolation(): void
    {
        $violations = $this->validator->validate('P10', new CouponExists());

        self::assertCount(0, $violations);
    }

    public function testMissingCouponRaisesViolation(): void
    {
        $violations = $this->validator->validate('NONEXISTENT', new CouponExists());

        self::assertCount(1, $violations);
        self::assertSame('Coupon code "NONEXISTENT" does not exist.', $violations->get(0)->getMessage());
    }

    public function testNullValueRaisesNoViolation(): void
    {
        $violations = $this->validator->validate(null, new CouponExists());

        self::assertCount(0, $violations);
    }
}
