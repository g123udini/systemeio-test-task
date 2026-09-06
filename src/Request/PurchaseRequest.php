<?php

declare(strict_types=1);

namespace SystemeioTestTask\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use SystemeioTestTask\Validator\Constraint\PaymentProcessorSupported;

final class PurchaseRequest extends AbstractPricingRequest
{
    #[Assert\NotBlank(message: 'Payment processor is required.')]
    #[Assert\Type(type: 'string', message: 'Payment processor must be a string.')]
    #[PaymentProcessorSupported]
    private readonly mixed $paymentProcessor;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->paymentProcessor = $request->request->get('paymentProcessor');
    }

    public function getPaymentProcessorId(): string
    {
        return (string) $this->paymentProcessor;
    }
}
