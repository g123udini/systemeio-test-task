<?php

declare(strict_types=1);

namespace SystemeioTestTask\Validator\Constraint;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class TaxNumber extends Constraint
{
    public string $message = 'Tax number "{{ value }}" is not a valid tax number for a supported country.';
}
