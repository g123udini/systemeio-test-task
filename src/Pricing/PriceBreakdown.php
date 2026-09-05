<?php

declare(strict_types=1);

namespace SystemeioTestTask\Pricing;

use JsonSerializable;
use Override;

final readonly class PriceBreakdown implements JsonSerializable
{
    public function __construct(
        public int $productPriceCents,
        public int $discountCents,
        public int $taxRatePercent,
        public int $taxAmountCents,
        public int $totalPriceCents,
    ) {
    }

    /**
     * @return array{productPrice: float, discount: float, taxRatePercent: int, taxAmount: float, price: float}
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'productPrice' => self::centsToEuros($this->productPriceCents),
            'discount' => self::centsToEuros($this->discountCents),
            'taxRatePercent' => $this->taxRatePercent,
            'taxAmount' => self::centsToEuros($this->taxAmountCents),
            'price' => self::centsToEuros($this->totalPriceCents),
        ];
    }

    private static function centsToEuros(int $cents): float
    {
        return round($cents / 100, 2);
    }
}
