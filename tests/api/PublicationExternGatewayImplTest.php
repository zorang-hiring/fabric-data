<?php
declare(strict_types=1);

namespace App\Tests\api;

use App\api\Exception\UnexpectedExternalPublicationsException;
use App\api\Model\PublicationModelCollection;
use App\api\Model\PublicationModelFactory;
use App\api\PublicationExternGatewayImpl;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\HttpFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class PublicationExternGatewayImplTest extends TestCase
{
    const EXPECTED_EXTERNAL_ENDPOINT = 'http://www.omdbapi.com/?s=The+Matrix&apikey=720c3666';
    protected PublicationExternGatewayImpl $gateway;
    protected MockObject|Client $httpClient;
    protected PublicationModelFactory $factory;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = $this->mockHttpClient();
        $this->gateway = new PublicationExternGatewayImpl(
            $this->httpClient,
            '720c3666'
        );
        $this->factory = new PublicationModelFactory();
    }

    public function dataProvider_error(): array
    {
        return [
            [
                $this->makeHttpResponse(
                    400,
                    file_get_contents(__DIR__ . '/fixture/external-service-response.json')
                )
            ],
            [
                $this->makeHttpResponse(200, 'not json')
            ],
            [
                $this->makeHttpResponse(200, json_encode(['unexpected' => 'json']))
            ]
        ];
    }

    /**
     * @dataProvider dataProvider_error
     */
    public function testTestSearch_error($response)
    {
        // GIVEN
        $this->httpClient->expects(self::once())
            ->method('request')
            ->with('get', self::EXPECTED_EXTERNAL_ENDPOINT)
            ->willReturn($response);

        self::expectException(UnexpectedExternalPublicationsException::class);

        // WHEN
        $this->gateway->search('The Matrix');
    }

    public function testTestSearch_success()
    {
        // GIVEN
        $this->httpClient->expects(self::once())
            ->method('request')
            ->with('get', self::EXPECTED_EXTERNAL_ENDPOINT)
            ->willReturn($this->makeHttpResponse(
                200,
                file_get_contents(__DIR__ . '/fixture/external-service-response.json')
            ));

        // WHEN
        $result = $this->gateway->search('The Matrix');

        // THEN
        self::assertEquals(
            (new PublicationModelCollection())
                ->addItem($this->factory->makeOne([
                    'externalId' => 'tt0133093',
                    'type' => 'movie',
                    'title' => 'The Matrix',
                    'year' => 1999,
                    'poster' => 'https://m.media-amazon.com/images/M/MV5BNzQzOTk3OTAtNDQ0Zi00ZTVkLWI0MTEtMDllZjNkYzNjNTc4L2ltYWdlXkEyXkFqcGdeQXVyNjU0OTQ0OTY@._V1_SX300.jpg',
                ]))
                ->addItem($this->factory->makeOne([
                    'externalId' => 'tt0234215',
                    'type' => 'game',
                    'title' => 'The Matrix Reloaded',
                    'year' => 2003,
                    'poster' => 'https://m.media-amazon.com/images/M/MV5BODE0MzZhZTgtYzkwYi00YmI5LThlZWYtOWRmNWE5ODk0NzMxXkEyXkFqcGdeQXVyNjU0OTQ0OTY@._V1_SX300.jpg',
                ]))
                ->addItem($this->factory->makeOne([
                    'externalId' => '',
                    'type' => '',
                    'title' => '',
                    'year' => '',
                    'poster' => null,
                ])),
            $result
        );
    }

    protected function mockHttpClient(): MockObject|ClientInterface
    {
        return self::getMockBuilder(ClientInterface::class)
            ->getMockForAbstractClass();
    }

    protected function makeHttpResponse(int $code, bool|string $content): ResponseInterface
    {
        $httpFactory = new HttpFactory();
        return $httpFactory
            ->createResponse($code)
            ->withBody(
                $httpFactory->createStream($content)
            );
    }
}
