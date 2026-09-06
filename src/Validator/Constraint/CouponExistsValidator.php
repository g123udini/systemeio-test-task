<?php

declare(strict_types=1);

namespace SystemeioTestTask\Validator\Constraint;

use Override;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use SystemeioTestTask\Repository\CouponRepository;
use function is_string;

final class CouponExistsValidator extends ConstraintValidator
{
    public function __construct(private readonly CouponRepository $couponRepository)
    {
    }

    #[Override]
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CouponExists) {
            throw new UnexpectedTypeException($constraint, CouponExists::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            // Wrong type is reported by Assert\Type; this constraint only checks existence.
            return;
        }

        if (null === $this->couponRepository->findOneByCode($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
