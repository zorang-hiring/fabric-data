<?php
declare(strict_types=1);

namespace App\api;

class PublicationServiceImpl implements PublicationService
{
    public function __construct(
        protected PublicationExternGateway $externGateway,
        protected PublicationLocalStorage $localStorage
    ){}

    public function handle(string $filterByTitle): PublicationDtoCollection
    {
        $externalResult = $this->externGateway->search($filterByTitle);
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
            if (!$localResult->hasWithSameExternalId($externItem)) {
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
            if (!$toReturn->hasWithSameExternalId($localItem)) {
                $toReturn->addItem($localItem);
            }
        }
        return $toReturn;
    }
}