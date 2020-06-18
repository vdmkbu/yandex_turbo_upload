<?php

use Slim\App;
use Slim\Middleware\ErrorMiddleware;
use App\Http\Middleware;

return function (App $app) {
    $app->add(Middleware\HeaderAuth::class);

    // Parse json, form data and xml
    $app->addBodyParsingMiddleware();

    // Add the Slim built-in routing middleware
    $app->addRoutingMiddleware();

    // Catch exceptions and errors
    $app->add(ErrorMiddleware::class);
};