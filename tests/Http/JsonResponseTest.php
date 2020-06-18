<?php

namespace App\Test\Http;


use App\Http\JsonResponse;
use PHPUnit\Framework\TestCase;

class JsonResponseTest extends TestCase
{
    public function testWithCode()
    {
        $response = new JsonResponse(0, 201);

        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        self::assertEquals('0', $response->getBody()->getContents());
        self::assertEquals(201, $response->getStatusCode());
    }
}