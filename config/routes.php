<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

return function (App $app) {
    $app->get('/', \App\Http\Action\HomeAction::class);
    $app->post('/upload', \App\Http\Action\UploadAction::class);
};