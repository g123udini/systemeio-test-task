<?php

declare(strict_types=1);

namespace SystemeioTestTask\Tests\Validator\Constraint;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use SystemeioTestTask\Validator\Constraint\TaxNumber;
use SystemeioTestTask\Validator\Constraint\TaxNumberValidator;

#[CoversClass(TaxNumberValidator::class)]
#[AllowMockObjectsWithoutExpectations]
final class TaxNumberValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new TaxNumberValidator();
    }

    #[DataProvider('validTaxNumberProvider')]
    public function testValidTaxNumberRaisesNoViolation(string $taxNumber): void
    {
        $this->validator->validate($taxNumber, new TaxNumber());

        $this->assertNoViolation();
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function validTaxNumberProvider(): iterable
    {
        yield 'Germany' => ['DE123456789'];

        yield 'Italy' => ['IT12345678900'];

        yield 'France' => ['FRAB123456789'];

        yield 'Greece' => ['GR123456789'];

        yield 'lowercase country prefix' => ['de123456789'];

        yield 'lowercase French VAT key' => ['FRab123456789'];
    }

    #[DataProvider('invalidTaxNumberProvider')]
    public function testInvalidTaxNumberRaisesViolation(string $taxNumber): void
    {
        $constraint = new TaxNumber();

        $this->validator->validate($taxNumber, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $taxNumber)
            ->assertRaised();
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidTaxNumberProvider(): iterable
    {
        yield 'unsupported country' => ['US123456789'];

        yield 'wrong digit count' => ['DE12345'];

        yield 'no country prefix' => ['123456789'];
    }

    public function testNullValueRaisesNoViolation(): void
    {
        $this->validator->validate(null, new TaxNumber());

        $this->assertNoViolation();
    }
}
