<?php

declare(strict_types=1);

namespace SystemeioTestTask\Request;

use DDH\ComponentBundle\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use SystemeioTestTask\Validator\Constraint\CouponExists;
use SystemeioTestTask\Validator\Constraint\PaymentProcessorSupported;
use SystemeioTestTask\Validator\Constraint\ProductExists;
use SystemeioTestTask\Validator\Constraint\TaxNumber;

final class PurchaseRequest implements RequestInterface
{
    #[Assert\NotNull(message: 'Product is required.')]
    #[Assert\Type(type: 'int', message: 'Product must be an integer.')]
    #[ProductExists]
    private readonly mixed $product;

    #[Assert\NotBlank(message: 'Tax number is required.')]
    #[Assert\Type(type: 'string', message: 'Tax number must be a string.')]
    #[TaxNumber]
    private readonly mixed $taxNumber;

    #[Assert\Type(type: 'string', message: 'Coupon code must be a string.')]
    #[CouponExists]
    private readonly mixed $couponCode;

    #[Assert\NotBlank(message: 'Payment processor is required.')]
    #[Assert\Type(type: 'string', message: 'Payment processor must be a string.')]
    #[PaymentProcessorSupported]
    private readonly mixed $paymentProcessor;

    public function __construct(Request $request)
    {
        $this->product = $request->request->get('product');
        $this->taxNumber = $request->request->get('taxNumber');
        $this->couponCode = $request->request->get('couponCode');
        $this->paymentProcessor = $request->request->get('paymentProcessor');
    }

    public function getProductId(): int
    {
        return (int) $this->product;
    }

    public function getTaxNumber(): string
    {
        return (string) $this->taxNumber;
    }

    public function getCouponCode(): ?string
    {
        if (null === $this->couponCode || '' === $this->couponCode) {
            return null;
        }

        return (string) $this->couponCode;
    }

    public function getPaymentProcessorId(): string
    {
        return (string) $this->paymentProcessor;
    }
}
