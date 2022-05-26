<?php
declare(strict_types=1);

namespace App\Tests\api;

use App\api\PublicationController;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SHaus\TppAuthCmp\User;

class PublicationControllerTest extends TestCase
{
    protected PublicationController $controller;
    protected PublicationServiceSpy $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PublicationServiceSpy();
        $this->controller = new PublicationController(
            $this->service
        );
    }

    public function testHandle()
    {
        // WHEN
        $this->controller->handle(
            (new ServerRequest('GET', 'https://some.com'))
                ->withAttribute('q', 'some title')
            ,
            $response = new \GuzzleHttp\Psr7\Response()
        );

        // THEN
        self::assertSame(200, $response->getStatusCode());
        $response->getBody()->rewind();
        self::assertSame(json_encode([
            'success' => 'OK',
            'result' => [
                [
                    'externalId' => 'externId1',
                    'type' => 'type1',
                    'title' => 'title1',
                    'year' => 2001,
                    'poster' => 'poster1',
                ],
                [
                    'externalId' => 'externId2',
                    'type' => 'type2',
                    'title' => 'title2',
                    'year' => 2002,
                    'poster' => 'poster2',
                ],
            ]
        ]), $response->getBody()->getContents());
        self::assertSame('some title', $this->service->getFilteredBy());
    }
}
