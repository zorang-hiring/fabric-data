<?php
declare(strict_types=1);

namespace App\api;

use App\api\Exception\UnexpectedExternalPublicationsException;
use GuzzleHttp\ClientInterface;

class PublicationExternGatewayImpl implements PublicationExternGateway
{
    protected PublicationDtoFactory $publicationFactory;

    public function __construct(
        protected ClientInterface $client
    ){
        $this->publicationFactory = new PublicationDtoFactory();
    }

    public function search(string $title) : PublicationDtoCollection
    {
        $response = $this->client->request(
            'get',
            'http://www.omdbapi.com/?s=' . urlencode($title) . '&apikey=720c3666' // todo move api key
        );

        $responseJson = @json_decode($response->getBody()->getContents(), true);
        if (
            $response->getStatusCode() !== 200
            || !is_array($responseJson)
            || !array_key_exists('Search', $responseJson)
        ) {
            throw new UnexpectedExternalPublicationsException();
        }

        return $this->buildResult($responseJson['Search']);
    }

    protected function buildResult($search): PublicationDtoCollection
    {
        $result = new PublicationDtoCollection();
        foreach ($search as $item) {
            $result->addItem($this->buildPublication($item));
        }
        return $result;
    }

    protected function buildPublication(mixed $item): PublicationDto
    {
        return $this->publicationFactory->makeOne([
            'externalId' => $item['imdbID'],
            'type' => $item['Type'],
            'title' => $item['Title'],
            'year' => $item['Year'],
            'poster' => $item['Poster'],
        ]);
    }
}