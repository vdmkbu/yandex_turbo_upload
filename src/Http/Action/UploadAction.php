<?php

namespace App\Http\Action;

use App\Http\JsonResponse;
use App\Service\UploadService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;


class UploadAction implements RequestHandlerInterface
{

    private $service;

    public function __construct(UploadService $service)
    {
        $this->service = $service;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();

        $feed = $this->service->upload($data);

        echo $feed;

        return new JsonResponse($data);
    }
}