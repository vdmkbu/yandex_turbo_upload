<?php

namespace App\Http\Middleware;


use App\Http\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HeaderAuth implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->getHeaderLine('X-API-TOKEN');

        if ($token != getenv('AUTH_TOKEN')) {
           return new JsonResponse([
             'error_message' => 'Invalid auth token'
           ], 401);
        }

        return $handler->handle($request);
    }

}