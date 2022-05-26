<?php
declare(strict_types=1);

namespace App\Tests\api;

use App\api\Model\PublicationDtoCollection;
use App\api\Model\PublicationDtoFactory;
use App\api\PublicationService;

class PublicationServiceSpy implements PublicationService
{
    protected string $filteredBy;

    public function getFilteredBy()
    {
        return $this->filteredBy;
    }

    public function handle(string $filterByTitle): PublicationDtoCollection
    {
        $this->filteredBy = $filterByTitle;

        $factory = new PublicationDtoFactory();
        return (new PublicationDtoCollection())
            ->addItem($factory->makeOne([
                'externalId' => 'externId1',
                'type' => 'type1',
                'title' => 'title1',
                'year' => '2001',
                'poster' => 'poster1',
            ]))
            ->addItem($factory->makeOne([
                'externalId' => 'externId2',
                'type' => 'type2',
                'title' => 'title2',
                'year' => '2002',
                'poster' => 'poster2',
            ]));
    }
}