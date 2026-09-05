<?php

declare(strict_types=1);

namespace SystemeioTestTask\Validator\Constraint;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class ProductExists extends Constraint
{
    public string $message = 'Product "{{ value }}" does not exist.';
}
