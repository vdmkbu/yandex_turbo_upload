<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeAction
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $response->getBody()->write(json_encode([
            'name' => 'API',
            'version' => '1.0'
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
}