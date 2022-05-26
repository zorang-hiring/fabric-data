<?php
declare(strict_types=1);

namespace App\api;
use Psr\Http\Message\ResponseInterface as Response;

class PublicationController
{
    public function __construct(
        public PublicationService $service
    ){}

    public function handle($byTitle, Response $response)
    {
        $response->getBody()->write(json_encode([
            'success' => 'OK',
            'result' => $this->service->handle($byTitle)
        ]));
    }
}