<?php

declare(strict_types=1);

namespace SystemeioTestTask\Tests\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use SystemeioTestTask\Controller\CalculatePriceController;
use function json_decode;
use function json_encode;
use const JSON_THROW_ON_ERROR;

#[CoversClass(CalculatePriceController::class)]
final class CalculatePriceControllerTest extends WebTestCase
{
    public function testCalculatesPriceWithoutCoupon(): void
    {
        $client = static::createClient();

        $client->request('POST', '/calculate-price', server: ['CONTENT_TYPE' => 'application/json'], content: $this->body([
            'product' => 1,
            'taxNumber' => 'DE123456789',
        ]));

        self::assertResponseIsSuccessful();
        self::assertSame(119.0, (float) $this->responseData($client)['data']['price']);
    }

    public function testCalculatesPriceWithPercentCoupon(): void
    {
        $client = static::createClient();

        $client->request('POST', '/calculate-price', server: ['CONTENT_TYPE' => 'application/json'], content: $this->body([
            'product' => 1,
            'taxNumber' => 'GR123456789',
            'couponCode' => 'P10',
        ]));

        self::assertResponseIsSuccessful();
        self::assertSame(111.6, $this->responseData($client)['data']['price']);
    }

    public function testInvalidTaxNumberReturns400(): void
    {
        $client = static::createClient();

        $client->request('POST', '/calculate-price', server: ['CONTENT_TYPE' => 'application/json'], content: $this->body([
            'product' => 1,
            'taxNumber' => 'XX123',
        ]));

        self::assertResponseStatusCodeSame(400);
    }

    public function testUnknownCouponReturns400(): void
    {
        $client = static::createClient();

        $client->request('POST', '/calculate-price', server: ['CONTENT_TYPE' => 'application/json'], content: $this->body([
            'product' => 1,
            'taxNumber' => 'DE123456789',
            'couponCode' => 'NOPE',
        ]));

        self::assertResponseStatusCodeSame(400);
    }

    public function testUnknownProductReturns400(): void
    {
        $client = static::createClient();

        $client->request('POST', '/calculate-price', server: ['CONTENT_TYPE' => 'application/json'], content: $this->body([
            'product' => 999999,
            'taxNumber' => 'DE123456789',
        ]));

        self::assertResponseStatusCodeSame(400);
    }

    public function testWrongTypeProductReturns400NotServerError(): void
    {
        $client = static::createClient();

        $client->request('POST', '/calculate-price', server: ['CONTENT_TYPE' => 'application/json'], content: $this->body([
            'product' => '1',
            'taxNumber' => 'DE123456789',
        ]));

        self::assertResponseStatusCodeSame(400);
    }

    public function testEmptyStringCouponCodeIsTreatedAsNoCoupon(): void
    {
        $client = static::createClient();

        $client->request('POST', '/calculate-price', server: ['CONTENT_TYPE' => 'application/json'], content: $this->body([
            'product' => 1,
            'taxNumber' => 'DE123456789',
            'couponCode' => '',
        ]));

        self::assertResponseIsSuccessful();
        self::assertSame(119.0, (float) $this->responseData($client)['data']['price']);
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
