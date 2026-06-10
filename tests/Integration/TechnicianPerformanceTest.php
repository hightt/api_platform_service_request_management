<?php

declare(strict_types=1);

namespace App\Tests\Integration\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

final class TechnicianPerformanceTest extends ApiTestCase
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
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/ld+json',
            ],
        ]);

        return $client;
    }

    public function testEndpointRequiresAuthentication(): void
    {
        static::createClient()->request('GET', '/api/technicians/stats');

        self::assertResponseStatusCodeSame(401);
    }

    public function testTechnicianStatsEndpoint(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', '/api/technicians/stats');

        self::assertResponseIsSuccessful();

        $data = $client->getResponse()->toArray();

        self::assertIsArray($data);
    }
}
