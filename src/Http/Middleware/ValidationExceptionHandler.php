<?php

namespace App\Http\Middleware;


use App\Http\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ValidationExceptionHandler implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {

            return $handler->handle($request);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error_message' => $e->getMessage(),
            ], 422);
        }
    }

}