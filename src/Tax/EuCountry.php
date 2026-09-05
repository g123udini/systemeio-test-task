<?php

declare(strict_types=1);

namespace SystemeioTestTask\Tax;

enum EuCountry: string
{
    case Germany = 'DE';
    case Italy = 'IT';
    case France = 'FR';
    case Greece = 'GR';

    public function taxRatePercent(): int
    {
        return match ($this) {
            self::Germany => 19,
            self::Italy => 22,
            self::France => 20,
            self::Greece => 24,
        };
    }

    /**
     * @return non-empty-string
     */
    public function taxNumberPattern(): string
    {
        return match ($this) {
            self::Germany => '/^DE\d{9}$/',
            self::Italy => '/^IT\d{11}$/',
            self::France => '/^FR[A-Za-z]{2}\d{9}$/',
            self::Greece => '/^GR\d{9}$/',
        };
    }

    public static function fromTaxNumber(string $taxNumber): ?self
    {
        return self::tryFrom(strtoupper(substr($taxNumber, 0, 2)));
    }
}
