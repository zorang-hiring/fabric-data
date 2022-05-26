<?php
declare(strict_types=1);

namespace App\api;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PublicationController
{
    public function __construct(
        public PublicationService $service
    ){}

    public function __invoke(Request $request, Response $response)
    {
        $result = $this->service->handle(
            (string) $request->getAttribute('q')
        );

        $response->getBody()->write(json_encode([
            'success' => 'OK',
            'result' => $result
        ]));
        // $response->withHeader('Content-Type', 'application/json; charset=UTF-8'); // todo remove or enable
    }
}