<?php
declare(strict_types=1);

namespace App\api;

use App\api\Exception\UnexpectedExternalPublicationsException;
use Psr\Log\LoggerInterface;

class PublicationServiceImpl implements PublicationService
{
    public function __construct(
        protected PublicationExternGateway $externGateway,
        protected PublicationLocalStorage $localStorage,
        protected LoggerInterface $logger
    ){}

    public function handle(string $filterByTitle): PublicationDtoCollection
    {
        try {
            $externalResult = $this->externGateway->search($filterByTitle);
        } catch (UnexpectedExternalPublicationsException $e) {
            $this->logger->warning('Unexpected external gateway result');
            $externalResult = new PublicationDtoCollection();
        }
        $localResult = $this->localStorage->searchByTitle($filterByTitle);

        if (count($toSave = $this->getItemsToSaveLocally($externalResult, $localResult))) {
            $this->localStorage->save($toSave);
        }

        return $this->joinWithUnique($externalResult, $localResult);
    }

    protected function getItemsToSaveLocally(
        PublicationDtoCollection $externalResult,
        PublicationDtoCollection $localResult
    ): PublicationDtoCollection {

        $result = new PublicationDtoCollection();
        foreach ($externalResult->getAll() as $externItem) {
            if (!$localResult->hasEqual($externItem)) {
                $result->addItem($externItem);
            }
        }
        return $result;
    }

    protected function joinWithUnique(
        PublicationDtoCollection $externalResult,
        PublicationDtoCollection $localResult
    ): PublicationDtoCollection
    {
        $toReturn = clone $externalResult;
        foreach ($localResult->getAll() as $localItem) {
            if (!$toReturn->hasEqual($localItem)) {
                $toReturn->addItem($localItem);
            }
        }
        return $toReturn;
    }
}