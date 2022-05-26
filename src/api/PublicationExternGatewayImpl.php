<?php
declare(strict_types=1);

namespace App\api;

use App\api\Exception\UnexpectedExternalPublicationsException;
use App\api\Model\PublicationDto;
use App\api\Model\PublicationDtoCollection;
use App\api\Model\PublicationDtoFactory;
use GuzzleHttp\ClientInterface;

class PublicationExternGatewayImpl implements PublicationExternGateway
{
    protected PublicationDtoFactory $publicationFactory;

    public function __construct(
        protected ClientInterface $client,
        protected string $apiKey
    ){
        $this->publicationFactory = new PublicationDtoFactory();
    }

    public function search(string $title) : PublicationDtoCollection
    {
        $response = $this->client->request(
            'get',
            'http://www.omdbapi.com/?s=' . urlencode($title) . '&apikey=' . $this->apiKey
        );

        $responseJson = @json_decode($response->getBody()->getContents(), true);
        if ($this->isExpectedResponse($response, $responseJson)) {
            throw new UnexpectedExternalPublicationsException();
        }

        return $this->buildResult($responseJson['Search']);
    }

    protected function buildResult(array $externPublications): PublicationDtoCollection
    {
        $result = new PublicationDtoCollection();
        foreach ($externPublications as $externPublication) {
            $result->addItem($this->buildPublication($externPublication));
        }
        return $result;
    }

    protected function buildPublication(array $externPublication): PublicationDto
    {
        return $this->publicationFactory->makeOne([
            'externalId' => $externPublication['imdbID'],
            'type' => $externPublication['Type'],
            'title' => $externPublication['Title'],
            'year' => $externPublication['Year'],
            'poster' => $externPublication['Poster'],
        ]);
    }

    protected function isExpectedResponse(\Psr\Http\Message\ResponseInterface $response, mixed $responseJson): bool
    {
        return $response->getStatusCode() !== 200
            || !is_array($responseJson)
            || !array_key_exists('Search', $responseJson);
    }
}