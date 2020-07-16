<?php

namespace App\Test\Http;

use GuzzleHttp\Exception\ClientException;

class RequestTest extends WebTestCase
{
    /** @test */
    public function test_unauthorized_request()
    {

        $this->expectException(ClientException::class);
        $response = $this->client()->request('GET', '/', []);

        self::assertEquals(401, $response->getStatusCode());


    }

    /** @test */
    public function test_success_auth_request()
    {

        $headers = [
            'Content-Type' => 'application/json',
            'X-API-TOKEN' => getenv('AUTH_TOKEN')
        ];


        $response = $this->client($headers)->request('GET', '/', []);
        $response = json_decode($response->getBody()->getContents(), true);


        self::assertEquals('API', $response['name']);
        self::assertEquals('1.0', $response['version']);
    }
}