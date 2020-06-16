<?php

namespace App\Action;

use App\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeAction
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {

        return new JsonResponse([
            'name' => 'API',
            'version' => '1.0'
        ]);
    }
}