<?php

declare(strict_types=1);

namespace SystemeioTestTask\Validator\Constraint;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class CouponExists extends Constraint
{
    public string $message = 'Coupon code "{{ value }}" does not exist.';
}
