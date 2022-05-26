<?php
declare(strict_types=1);

namespace App\api;

use App\api\Exception\UnexpectedExternalPublicationsException;
use App\api\Model\PublicationModelCollection;
use App\api\Repository\PublicationRepository;
use Psr\Log\LoggerInterface;

class PublicationServiceImpl implements PublicationService
{
    public function __construct(
        protected PublicationExternGateway $externGateway,
        protected PublicationInternalStore $internalStorage,
        protected ?LoggerInterface         $logger = null
    ){}

    public function handle(string $filterByTitle): PublicationModelCollection
    {
        $externalResult = $this->searchExternalPublications($filterByTitle);
        $localResult = $this->internalStorage->searchByTitle($filterByTitle);

        if (count($toSave = $this->getItemsToSaveLocally($externalResult, $localResult))) {
            $this->internalStorage->store($toSave);
        }

        return $this->joinWithUnique($externalResult, $localResult);
    }

    protected function searchExternalPublications(string $filterByTitle): PublicationModelCollection
    {
        try {
            $externalResult = $this->externGateway->search($filterByTitle);
        } catch (UnexpectedExternalPublicationsException $e) {
            // if external publications can not be obtained then log warning and return empty
            // publications to avoid braking if frontend
            $this->logger?->warning('Unexpected external gateway result');
            $externalResult = new PublicationModelCollection();
        }
        return $externalResult;
    }

    protected function getItemsToSaveLocally(
        PublicationModelCollection $externalResult,
        PublicationModelCollection $localResult
    ): PublicationModelCollection {

        $result = new PublicationModelCollection();
        foreach ($externalResult->getAll() as $externItem) {
            if (!$localResult->hasEqual($externItem)) {
                $result->addItem($externItem);
            }
        }
        return $result;
    }

    protected function joinWithUnique(
        PublicationModelCollection $externalResult,
        PublicationModelCollection $localResult
    ): PublicationModelCollection
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