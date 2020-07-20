<?php

use App\Http\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Middleware\ErrorMiddleware;
use App\Http\Middleware;


return function (App $app) {
    $app->add(Middleware\ValidationExceptionHandler::class);
    $app->add(Middleware\HeaderAuth::class);

    // Parse json, form data and xml
    $app->addBodyParsingMiddleware();

    // Add the Slim built-in routing middleware
    $app->addRoutingMiddleware();

    // HttpNotFound Middleware
    $app->add(function (
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ) {
        try {
            return $handler->handle($request);
        } catch (HttpNotFoundException $httpException) {
            return new JsonResponse(['error_message' => 'page not found'], 404);
        }
        catch (HttpMethodNotAllowedException $httpException) {
            return new JsonResponse(['error_message' => $httpException->getMessage()], 405);
        }
        catch (Exception $exception) {
            return new JsonResponse(['error_message' => $exception->getMessage()], 500);
        }

    });

    // Catch exceptions and errors
    $app->add(ErrorMiddleware::class);
};