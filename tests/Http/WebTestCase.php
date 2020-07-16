<?php

namespace App\Test\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;

class WebTestCase extends TestCase
{
    public function client($headers = [])
    {
        $client = new Client( [
            'base_uri' => 'http://nginx',
            'timeout'  => 2.0,
            'headers' => $headers
        ]);

        return $client;
    }
}