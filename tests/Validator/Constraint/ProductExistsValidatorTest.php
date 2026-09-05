<?php

declare(strict_types=1);

namespace SystemeioTestTask\Tests\Validator\Constraint;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use SystemeioTestTask\Validator\Constraint\ProductExists;
use SystemeioTestTask\Validator\Constraint\ProductExistsValidator;

#[CoversClass(ProductExistsValidator::class)]
final class ProductExistsValidatorTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testExistingProductRaisesNoViolation(): void
    {
        $violations = $this->validator->validate(1, new ProductExists());

        self::assertCount(0, $violations);
    }

    public function testMissingProductRaisesViolation(): void
    {
        $violations = $this->validator->validate(999999, new ProductExists());

        self::assertCount(1, $violations);
        self::assertSame('Product "999999" does not exist.', $violations->get(0)->getMessage());
    }

    public function testNullValueRaisesNoViolation(): void
    {
        $violations = $this->validator->validate(null, new ProductExists());

        self::assertCount(0, $violations);
    }
}
