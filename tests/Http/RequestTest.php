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

    /** @test */
    public function test_empty_messages()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'X-API-TOKEN' => getenv('AUTH_TOKEN')
        ];

        $this->expectException(ClientException::class);

        $response = $this->client($headers)->request('POST', '/upload', []);
        self::assertEquals(422, $response->getStatusCode());

        $response = $this->client($headers)->request('POST', '/delete', []);
        self::assertEquals(422, $response->getStatusCode());
    }

    /** @test */
    public function test_success_upload_not_prod()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'X-API-TOKEN' => getenv('AUTH_TOKEN')
        ];

        $response = $this->client($headers)->request('POST', '/upload', [
            'form_params' => [
                'messages' => [108351],
                'prod' => 0
            ]
        ]);

        $response = json_decode($response->getBody()->getContents(), true);

        self::assertNotEmpty($response['feed']);
    }

    /** @test */
    public function test_success_upload_prod()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'X-API-TOKEN' => getenv('AUTH_TOKEN')
        ];

        $response = $this->client($headers)->request('POST', '/upload', [
            'form_params' => [
                'messages' => [108351],
                'prod' => 1
            ]
        ]);

        $response = json_decode($response->getBody()->getContents(), true);

        self::assertNotEmpty($response['task_id']);
    }

    /** @test */
    public function test_success_delete_not_prod()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'X-API-TOKEN' => getenv('AUTH_TOKEN')
        ];

        $response = $this->client($headers)->request('POST', '/delete', [
            'form_params' => [
                'messages' => [108351],
                'prod' => 0
            ]
        ]);

        $response = json_decode($response->getBody()->getContents(), true);

        self::assertNotEmpty($response['feed']);
    }

    /** @test */
    public function test_success_delete_prod()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'X-API-TOKEN' => getenv('AUTH_TOKEN')
        ];

        $response = $this->client($headers)->request('POST', '/delete', [
            'form_params' => [
                'messages' => [108351],
                'prod' => 1
            ]
        ]);

        $response = json_decode($response->getBody()->getContents(), true);

        self::assertNotEmpty($response['task_id']);
    }
}