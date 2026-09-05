<?php

declare(strict_types=1);

namespace SystemeioTestTask\Tests\Tax;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SystemeioTestTask\Tax\EuCountry;

#[CoversClass(EuCountry::class)]
final class EuCountryTest extends TestCase
{
    #[DataProvider('taxRateProvider')]
    public function testTaxRatePercent(EuCountry $country, int $expectedRate): void
    {
        self::assertSame($expectedRate, $country->taxRatePercent());
    }

    /**
     * @return iterable<string, array{EuCountry, int}>
     */
    public static function taxRateProvider(): iterable
    {
        yield 'Germany' => [EuCountry::Germany, 19];

        yield 'Italy' => [EuCountry::Italy, 22];

        yield 'France' => [EuCountry::France, 20];

        yield 'Greece' => [EuCountry::Greece, 24];
    }

    #[DataProvider('validTaxNumberProvider')]
    public function testFromTaxNumberMatchesValidNumbers(string $taxNumber, EuCountry $expected): void
    {
        $country = EuCountry::fromTaxNumber($taxNumber);

        self::assertSame($expected, $country);
        self::assertSame(1, preg_match($country->taxNumberPattern(), $taxNumber));
    }

    /**
     * @return iterable<string, array{string, EuCountry}>
     */
    public static function validTaxNumberProvider(): iterable
    {
        yield 'Germany' => ['DE123456789', EuCountry::Germany];

        yield 'Italy' => ['IT12345678900', EuCountry::Italy];

        yield 'France' => ['FRAB123456789', EuCountry::France];

        yield 'Greece' => ['GR123456789', EuCountry::Greece];
    }

    public function testFromTaxNumberReturnsNullForUnsupportedCountry(): void
    {
        self::assertNull(EuCountry::fromTaxNumber('US123456789'));
    }
}
