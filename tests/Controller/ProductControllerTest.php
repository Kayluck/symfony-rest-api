<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    private function getToken(): string
    {
        $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'user@example.com',
            'password' => 'password123',
        ]));

        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);

        // Debug the response content
        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Login failed: ' . $content);
        }

        if (!isset($data['token'])) {
            throw new \Exception('Token not found in response: ' . $content);
        }

        return $data['token'];
    }

    public function testIndex(): void
    {
        $token = $this->getToken();

        $this->client->request('GET', '/api/products', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCreate(): void
    {
        $token = $this->getToken();

        $this->client->request('POST', '/api/products', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ], json_encode([
            'name' => 'Test Product',
            'description' => 'This is a test product',
            'price' => '10.99',
        ]));

        $response = $this->client->getResponse();
        $this->assertEquals(201, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
    }

    public function testShow(): void
    {
        $token = $this->getToken();

        // First create a product
        $this->client->request('POST', '/api/products', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ], json_encode([
            'name' => 'Test Product',
            'description' => 'This is a test product',
            'price' => '10.99',
        ]));

        $createResponse = $this->client->getResponse();
        $data = json_decode($createResponse->getContent(), true);
        $productId = $data['id'];

        // Now test showing that product
        $this->client->request('GET', '/api/products/' . $productId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('id', json_decode($response->getContent(), true));
    }

    public function testUpdate(): void
    {
        $token = $this->getToken();

        // First create a product
        $this->client->request('POST', '/api/products', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ], json_encode([
            'name' => 'Test Product',
            'description' => 'This is a test product',
            'price' => '10.99',
        ]));

        $createResponse = $this->client->getResponse();
        $data = json_decode($createResponse->getContent(), true);
        $productId = $data['id'];

        // Now update that product
        $this->client->request('PUT', '/api/products/' . $productId, [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ], json_encode([
            'name' => 'Updated Product',
            'description' => 'This is an updated test product',
            'price' => '20.99',
        ]));

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $updatedData = json_decode($response->getContent(), true);
        $this->assertEquals('Updated Product', $updatedData['name']);
    }

    public function testDelete(): void
    {
        $token = $this->getToken();

        // First create a product
        $this->client->request('POST', '/api/products', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ], json_encode([
            'name' => 'Test Product',
            'description' => 'This is a test product',
            'price' => '10.99',
        ]));

        $createResponse = $this->client->getResponse();
        $data = json_decode($createResponse->getContent(), true);
        $productId = $data['id'];

        // Now delete that product
        $this->client->request('DELETE', '/api/products/' . $productId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }
}
