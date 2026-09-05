<?php

declare(strict_types=1);

namespace SystemeioTestTask\Enum;

enum CouponType: string
{
    case Fixed = 'fixed';
    case Percent = 'percent';
}
