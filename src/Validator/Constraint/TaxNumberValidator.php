<?php

declare(strict_types=1);

namespace SystemeioTestTask\Validator\Constraint;

use Override;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use SystemeioTestTask\Tax\EuCountry;
use function is_string;

final class TaxNumberValidator extends ConstraintValidator
{
    #[Override]
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof TaxNumber) {
            throw new UnexpectedTypeException($constraint, TaxNumber::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            // Wrong type is reported by Assert\Type; this constraint only checks the format.
            return;
        }

        $normalizedValue = strtoupper($value);
        $country = EuCountry::fromTaxNumber($normalizedValue);

        if (null === $country || 1 !== preg_match($country->taxNumberPattern(), $normalizedValue)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
