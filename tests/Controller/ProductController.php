<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    public function testGetProducts()
    {
        $client = static::createClient();
        $client->request('GET', '/api/products');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreateProduct()
    {
        $client = static::createClient();
        $client->request('POST', '/api/products', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'Test Product',
            'description' => 'This is a test product',
            'price' => 99.99
        ]));

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

}
