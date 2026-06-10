<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

final class DeviceApiTest extends ApiTestCase
{
    private function createAuthenticatedClient()
    {
        $client = static::createClient();

        $response = $client->request('POST', '/api/login_check', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'username' => 'tech_active_1',
                'password' => 'tech123',
            ],
        ]);

        self::assertResponseIsSuccessful();

        $token = $response->toArray()['token'];

        $client->setDefaultOptions([
            'headers' => [
                'Authorization' => sprintf('Bearer %s', $token),
                'Content-Type' => 'application/ld+json',
                'Accept' => 'application/ld+json',
            ],
        ]);

        return $client;
    }

    public function testEndpointRequiresAuthentication(): void
    {
        static::createClient()->request('GET', '/api/devices');

        self::assertResponseStatusCodeSame(401);
    }

    public function testCreateDevice(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request('POST', '/api/devices', [
            'json' => [
                'serialNumber' => 'SN-123456',
                'model' => 'iPhone 15',
                'customerName' => 'John Doe',
            ],
        ]);

        self::assertResponseStatusCodeSame(201);

        self::assertJsonContains([
            'serialNumber' => 'SN-123456',
            'model' => 'iPhone 15',
            'customerName' => 'John Doe',
        ]);
    }

    public function testCreateDeviceValidation(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request('POST', '/api/devices', [
            'json' => [],
        ]);

        self::assertResponseStatusCodeSame(422);
    }

    public function testCreateDeviceWithDuplicateSerialNumber(): void
    {
        $client = $this->createAuthenticatedClient();

        $payload = [
            'serialNumber' => 'UNIQUE-SN-001',
            'model' => 'Samsung Galaxy S24',
            'customerName' => 'Jane Doe',
        ];

        $client->request('POST', '/api/devices', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(201);

        $client->request('POST', '/api/devices', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(422);
    }

    public function testGetDeviceCollection(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', '/api/devices', [
            'headers' => [
                'Accept' => 'application/ld+json',
            ],
        ]);

        self::assertResponseIsSuccessful();
    }

    public function testGetDevice(): void
    {
        $client = $this->createAuthenticatedClient();

        $response = $client->request('POST', '/api/devices', [
            'json' => [
                'serialNumber' => 'SN-GET-001',
                'model' => 'MacBook Pro',
                'customerName' => 'Alice',
            ],
        ]);

        self::assertResponseStatusCodeSame(201);

        $device = $response->toArray();

        $client->request(
            'GET',
            sprintf('/api/devices/%d', $device['id']),
            [
                'headers' => [
                    'Accept' => 'application/ld+json',
                ],
            ],
        );

        self::assertResponseIsSuccessful();

        self::assertJsonContains([
            'serialNumber' => 'SN-GET-001',
            'model' => 'MacBook Pro',
            'customerName' => 'Alice',
        ]);
    }

    public function testUpdateDevice(): void
    {
        $client = $this->createAuthenticatedClient();

        $response = $client->request('POST', '/api/devices', [
            'json' => [
                'serialNumber' => 'SN-PATCH-001',
                'model' => 'Old Model',
                'customerName' => 'Bob',
            ],
        ]);

        self::assertResponseStatusCodeSame(201);

        $device = $response->toArray();

        $client->request(
            'PATCH',
            sprintf('/api/devices/%d', $device['id']),
            [
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                    'Accept' => 'application/ld+json',
                ],
                'json' => [
                    'model' => 'New Model',
                ],
            ],
        );

        self::assertResponseIsSuccessful();

        self::assertJsonContains([
            'model' => 'New Model',
        ]);
    }

    public function testDeleteDevice(): void
    {
        $client = $this->createAuthenticatedClient();

        $response = $client->request('POST', '/api/devices', [
            'json' => [
                'serialNumber' => 'SN-DELETE-001',
                'model' => 'ThinkPad T14',
                'customerName' => 'Tom',
            ],
        ]);

        self::assertResponseStatusCodeSame(201);

        $device = $response->toArray();

        $client->request(
            'DELETE',
            sprintf('/api/devices/%d', $device['id']),
            [
                'headers' => [
                    'Accept' => 'application/ld+json',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(204);
    }
}
