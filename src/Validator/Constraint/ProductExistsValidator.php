<?php

declare(strict_types=1);

namespace SystemeioTestTask\Validator\Constraint;

use Override;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use SystemeioTestTask\Repository\ProductRepository;
use function is_int;

final class ProductExistsValidator extends ConstraintValidator
{
    public function __construct(private readonly ProductRepository $productRepository)
    {
    }

    #[Override]
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ProductExists) {
            throw new UnexpectedTypeException($constraint, ProductExists::class);
        }

        if (null === $value) {
            return;
        }

        if (!is_int($value)) {
            // Wrong type is reported by Assert\Type; this constraint only checks existence.
            return;
        }

        if (null === $this->productRepository->find($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', (string) $value)
                ->addViolation();
        }
    }
}
