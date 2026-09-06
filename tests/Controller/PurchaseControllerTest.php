<?php

declare(strict_types=1);

namespace SystemeioTestTask\Tests\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use SystemeioTestTask\Controller\PurchaseController;
use function json_decode;
use function json_encode;
use const JSON_THROW_ON_ERROR;

#[CoversClass(PurchaseController::class)]
final class PurchaseControllerTest extends WebTestCase
{
    public function testSuccessfulPurchaseViaPaypal(): void
    {
        $client = static::createClient();

        $client->request('POST', '/purchase', server: ['CONTENT_TYPE' => 'application/json'], content: $this->body([
            'product' => 1,
            'taxNumber' => 'IT12345678900',
            'couponCode' => 'D15',
            'paymentProcessor' => 'paypal',
        ]));

        self::assertResponseIsSuccessful();
        self::assertSame(103.7, $this->responseData($client)['data']['price']);
    }

    public function testStripeDeclinesBelowThreshold(): void
    {
        $client = static::createClient();

        $client->request('POST', '/purchase', server: ['CONTENT_TYPE' => 'application/json'], content: $this->body([
            // Чехол: 10 EUR + tax, well below Stripe's 100 EUR stub threshold
            'product' => 3,
            'taxNumber' => 'DE123456789',
            'paymentProcessor' => 'stripe',
        ]));

        self::assertResponseStatusCodeSame(422);
    }

    public function testFullyDiscountedPurchaseSucceedsAtZero(): void
    {
        $client = static::createClient();

        $client->request('POST', '/purchase', server: ['CONTENT_TYPE' => 'application/json'], content: $this->body([
            'product' => 2,
            'taxNumber' => 'GR123456789',
            'couponCode' => 'P100',
            'paymentProcessor' => 'paypal',
        ]));

        self::assertResponseIsSuccessful();
        self::assertSame(0.0, (float) $this->responseData($client)['data']['price']);
    }

    public function testUnsupportedPaymentProcessorReturns400(): void
    {
        $client = static::createClient();

        $client->request('POST', '/purchase', server: ['CONTENT_TYPE' => 'application/json'], content: $this->body([
            'product' => 1,
            'taxNumber' => 'DE123456789',
            'paymentProcessor' => 'bitcoin',
        ]));

        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function body(array $payload): string
    {
        return json_encode($payload, JSON_THROW_ON_ERROR);
    }

    /**
     * @return array{ok: bool, data: array<string, mixed>}
     */
    private function responseData(KernelBrowser $client): array
    {
        /** @var array{ok: bool, data: array<string, mixed>} $data */
        $data = json_decode($client->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);

        return $data;
    }
}
